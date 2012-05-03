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

namespace DoctrineORMModule\Authentication\Adapter;

use DoctrineModule\Authentication\Adapter\DoctrineObject,
    Doctrine\ORM\NoResultException,
    Doctrine\ORM\NonUniqueResultException;

/**
 * Authentication adapter that uses a Doctrine ORM Entity for verification.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   1.0
 * @version $Revision$
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 * @author Tim Roediger
 */
class DoctrineDocument extends DoctrineObject
{  
    /**
     * Prepares the query by building it from QueryBuilder based on the 
     * entity, credentialColumn and identityColumn.
     * 
     * @return Doctrine\ORM\Query
     */
    protected function authenticateCreateQuery()
    {
        $mdata = $this->em->getClassMetadata($this->entity);
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('q')
           ->from($this->entity, 'q')
           ->where($qb->expr()->eq(
                'q.' . $this->identityColumn,
                $qb->expr()->literal($this->identity)
            ));
           
        return $qb->getQuery();
    }
	
    /**
     * Validates the query. Catches exceptions from Doctrine and populates authenticate results
     * appropriately.
     * 
     * @return false|object
     */
    protected function authenticateValidateQuery(Query $query)
    {
        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            $this->authenticateResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $this->authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
        } catch (NonUniqueResultException $e) {
            $this->authenticateResultInfo['code'] = AuthenticationResult::FAILURE_IDENTITY_AMBIGUOUS;
            $this->authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
        }

        return false;
    }	
}
