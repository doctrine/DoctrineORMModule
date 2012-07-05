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
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

return array(
    'doctrine' => array(
        'connection' => array(),
        'cache' => array(
            'apc' => array(
                'class' => 'Doctrine\Common\Cache\ApcCache',
            ),
            'array' => array(
                'class' => 'Doctrine\Common\Cache\ArrayCache',
            ),
            'memcache' => array(
                'class'    => 'Doctrine\Common\Cache\Memcache',
                'instance' => 'my_memcache_alias',
            ),
            'memcached' => array(
                'class'    => 'Doctrine\Common\Cache\Memcached',
                'instance' => 'my_memcached_alias',
            ),
            'redis' => array(
                'class'    => 'Doctrine\Common\Cache\RedisCache',
                'instance' => 'my_redis_alias',
            ),
            'wincache' => array(
                'class' => 'Doctrine\Common\Cache\Wincache',
            ),
            'xcache' => array(
                'class' => 'Doctrine\Common\Cache\XcacheCache',
            ),
            'zenddata' => array(
                'class' => 'Doctrine\Common\Cache\ZendDataCache',
            )
        ),
    )
);