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
        'connections' => array(
            'orm_default' => array(
                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',
                'port'     => '3306',
                'user'     => 'root',
                'password' => '',
                'dbname'   => 'blitzaroo',
            )
        )
    ),

    'doctrine_orm_config' => array(
        'orm_default' => array(
            'auto_generate_proxies'     => true,
            'proxy_dir'                 => 'data/DoctrineORMModule/Proxy',
            'proxy_namespace'           => 'DoctrineORMModule\Proxy',

            'entity_namespaces'         => array(),

            'custom_datetime_functions' => array(),
            'custom_string_functions'   => array(),
            'custom_numeric_functions'  => array(),

            'named_queries'             => array(),
            'named_native_queries'      => array(),

            'sql_logger'                => null,
        )
    ),
);
