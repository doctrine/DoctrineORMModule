<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace DoctrineModule\Validator;

use Closure,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\EntityRepository,
    Zend\Validator\AbstractValidator,
    Zend\Validator\Exception;

/**
 * Base class for entity and no entity validators.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   1.0
 * @version $Revision$
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
abstract class AbstractEntity extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );
 
    /**
     * Doctrine EntityManager
     * 
     * @var EntityManager
     */
    protected $_em;
    
    /**
     * Name of the entity to use for validation.
     * 
     * @var string
     */
    protected $_entity;
    
    /**
     * Name of the field to use for validation.
     * 
     * @var string
     */
    protected $_field;

    /**
     * QueryBuilder closure.
     * 
     * @var Closure
     */
    protected $_queryBuilder;

    /**
     * Required options are:
     *  - em        EntityManager instance to use.
     *  - entity    Entity to use for validation.
     *  - field     Field to check.
     * 
     * Optional options are:
     *  - query_builder     Custom query_builder Closure to use for the select. A single
     *                      EntityRepository argument is passed at call time. Example:
     *                      'query_builder' => function($er) { return $er->createQueryBuilder('q'); } 
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('em', $options)) {
            throw new Exception\InvalidArgumentException('No EntityManager was specified.');
        }
        
        if (!$options['em'] instanceof EntityManager) {
            throw new Exception\InvalidArgumentException('Invalid EntityManager specified.');
        }
        $this->_em = $options['em'];
        
        if (!array_key_exists('entity', $options)) {
            throw new Exception\InvalidArgumentException('No entity class was specified.');
        }
        $this->_entity = $options['entity'];
        
        if (array_key_exists('query_builder', $options)) {
            if (!$options['query_builder'] instanceof Closure) {
                throw new Exception\InvalidArgumentException('query_builder must be a Closure');
            }
            $this->_queryBuilder = $options['query_builder'];
        } 
        $this->_field = $options['field'];
        
        parent::__construct($options);
    }

    /**
     * Generates a query based on the constructor options. Attempts to enforce
     * a minimum select for the query by checking the DQL parts for an alias
     * and then using the entity metadata to select only the identifiers.
     * 
     * @param mixed $value  Contains the value to check.
     * @return Doctrine\ORM\Query 
     */
    protected function _getQuery($value)
    {
        $em = $this->_em;
        $qb = $this->_queryBuilder;
        $field = $this->_field;
        
        // set default query builder closure if one wasn't specified
        if (!$qb) {
            $qb = function(EntityRepository $er) use ($field) {
                return $er->createQueryBuilder('q');
            };
        }

        $er = $em->getRepository($this->_entity);
        $qb = call_user_func($qb, $er);
        
        // reduce query to minimal return (selecting identifiers)
        $mdata = $em->getClassMetadata($this->_entity);
        $alias = current($qb->getDqlPart('from'))->getAlias();
        
        $qb->select("partial {$alias}.{" . implode(',', $mdata->identifier) . '}');
        
        // add field to query
        $qb->andWhere($qb->expr()->eq('q.' . $field, $qb->expr()->literal($value)))
           ->setMaxResults(1);
           
        return $qb->getQuery();
    }
}
