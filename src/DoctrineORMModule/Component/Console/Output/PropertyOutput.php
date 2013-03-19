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
 * and is licensed under the MIT license.
 */

namespace DoctrineORMModule\Component\Console\Output;

use \Symfony\Component\Console\Output\Output;
use \Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
 * Output writing in class member variable
 *
 * @license MIT
 * @author Aleksandr Sandrovskiy <a.sandrovsky@gmail.com>
 */
class PropertyOutput extends Output {

	/**
	 * @var
	 */
	private $message;

	/**
	 * @param int $verbosity
	 * @param null $decorated
	 * @param \Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter
	 */
	public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null) {
		if (null === $decorated) {
			$decorated = $this->hasColorSupport();
		}

		parent::__construct($verbosity, $decorated, $formatter);
	}

	/**
	 * @param string $message
	 * @param bool $newline
	 */
	protected function doWrite($message, $newline) {
		$this->message = $message;
	}

	/**
	 * @return mixed
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @return bool
	 */
	protected function hasColorSupport() {
		if (DIRECTORY_SEPARATOR == '\\') {
			return false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
		}

		return function_exists('posix_isatty') && @posix_isatty(STDOUT);
	}
}
