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

namespace DoctrineORMModule\Paginator;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Paginator adapter for Zend Paginator
 *
 * @author  Tõnis Tobre <tobre@bitweb.ee>
 * @license New BSD
 */
class Adapter implements AdapterInterface {

    /**
     * @var Paginator
     */
	protected $paginator;

	/**
	 * Constructor
	 *
	 * @param \Doctrine\ORM\AbstractQuery|QueryBuilder $query a query or query builder from which to get paginator items
	 */
	public function __construct($query) {
		$this->paginator = new Paginator($query);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getItems($offset, $itemCountPerPage) {
		$this->paginator->getQuery()->setFirstResult($offset)
			->setMaxResults($itemCountPerPage);

		return $this->paginator->getIterator();
	}

	/**
	 * {@inheritDoc}
	 */
	public function count() {
		return $this->paginator->count();
	}
}
