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

namespace DoctrineORMModule\Component\Console\Input;

/**
 * StringInput represents an input provided as a string.
 *
 * Usage:
 *
 *     $input = new StringInput('foo --bar="foobar"');
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Aleksandr Sandrovskiy <a.sandrovsky@gmail.com>
 *
 * @api
 */
class StringInput extends ArgvInput {

	const REGEX_STRING = '([^ ]+?)(?: |(?<!\\\\)"|(?<!\\\\)\'|$)';
	const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')';

	/**
	 * Constructor.
	 *
	 * @param string          $input      An array of parameters from the CLI (in the argv format)
	 * @param InputDefinition $definition A InputDefinition instance
	 *
	 * @api
	 */
	public function __construct($input, InputDefinition $definition = null) {
		parent::__construct(array(), $definition);

		$this->setTokens($this->tokenize($input));
	}

	/**
	 * Tokenizes a string.
	 *
	 * @param string $input The input to tokenize
	 *
	 * @return array An array of tokens
	 *
	 * @throws \InvalidArgumentException When unable to parse input (should never happen)
	 */
	private function tokenize($input) {
		$input = preg_replace('/(\r\n|\r|\n|\t)/', ' ', $input);

		$tokens = array();
		$length = strlen($input);
		$cursor = 0;
		while ($cursor < $length) {
			if (preg_match('/\s+/A', $input, $match, null, $cursor)) {
			} elseif (preg_match('/([^="\' ]+?)(=?)(' . self::REGEX_QUOTED_STRING . '+)/A', $input, $match, null, $cursor)) {
				$tokens[] = $match[1] . $match[2] . stripcslashes(str_replace(array('"\'', '\'"', '\'\'', '""'), '', substr($match[3], 1, strlen($match[3]) - 2)));
			} elseif (preg_match('/' . self::REGEX_QUOTED_STRING . '/A', $input, $match, null, $cursor)) {
				$tokens[] = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
			} elseif (preg_match('/' . self::REGEX_STRING . '/A', $input, $match, null, $cursor)) {
				$tokens[] = stripcslashes($match[1]);
			} else {
				// should never happen
				// @codeCoverageIgnoreStart
				throw new \InvalidArgumentException(sprintf('Unable to parse input near "... %s ..."', substr($input, $cursor, 10)));
				// @codeCoverageIgnoreEnd
			}

			$cursor += strlen($match[0]);
		}

		return $tokens;
	}
}
