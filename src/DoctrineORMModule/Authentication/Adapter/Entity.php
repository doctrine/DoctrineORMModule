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

namespace DoctrineModule\Authentication\Adapter;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\NoResultException,
    Doctrine\ORM\NonUniqueResultException,
    Doctrine\ORM\Query,
    Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Adapter\Exception,
    Zend\Authentication\Result as AuthenticationResult;

/**
 * Authentication adapter that uses a Doctrine Entity for verification.
 *
 * @deprecated please use DoctrineModule\Authentication\Adapter\DoctrineObject
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   0.1.0
 * @version $Revision$
 * @author  Kyle Spraggs <theman@spiffyjr.me>
 */
class DoctrineEntity extends DoctrineObject
{
    /**
     * {@inheritDoc}
     */
    public function __construct(
        EntityManager $em,
        $entity,
        $identityColumn = 'username',
        $credentialColumn = 'password',
        $credentialCallable = null
    ) {
        parent::__construct($em, $entity, $identityColumn, $credentialColumn, $credentialCallable);
    }


    /**
     * Sets the entity manager to use.
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @return \DoctrineModule\Authentication\Adapater\DoctrineEntity
     * @deprecated please use DoctrineModule\Authentication\Adapter\DoctrineObject
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->setObjectManager($em);
    }

    /**
     * Sets the entity to use for authentication.
     *
     * @param string $entity
     * @return \DoctrineModule\Authentication\Adapater\DoctrineEntity
     * @deprecated please use DoctrineModule\Authentication\Adapter\DoctrineObject
     */
    public function setEntity($entity)
    {
        $this->setIdentityClassName($entity);
        return $this;
    }

    /**
     * Set the value to be used as the identity
     *
     * @param  string $value
     * @return \DoctrineModule\Authentication\Adapater\DoctrineEntity
     * @deprecated please use DoctrineModule\Authentication\Adapter\DoctrineObject
     */
    public function setIdentity($value)
    {
        $this->setIdentityValue($value);
        return $this;
    }

    /**
     * Set the credential value to be used.
     *
     * @param  string $credential
     * @return \DoctrineModule\Authentication\Adapater\DoctrineEntity
     * @deprecated please use DoctrineModule\Authentication\Adapter\DoctrineObject
     */
    public function setCredential($credential)
    {
        $this->setCredentialValue($credential);
        return $this;
    }

    /**
     * Set the column name to be used as the identity column
     *
     * @param  string $identityColumn
     * @return \DoctrineModule\Authentication\Adapater\DoctrineEntity
     * @deprecated please use DoctrineModule\Authentication\Adapter\DoctrineObject
     */
    public function setIdentityColumn($identityColumn)
    {
        $this->setIdentityProperty($identityColumn);
        return $this;
    }

    /**
     * Set the column name to be used as the credential column
     *
     * @param  string $credentialColumn
     * @return \Zend\Authentication\Adapter\DbTable Provides a fluent interface
     * @deprecated please use DoctrineModule\Authentication\Adapter\DoctrineObject
     */
    public function setCredentialColumn($credentialColumn)
    {
        $this->setCredentialProperty($credentialColumn);
        return $this;
    }
}
