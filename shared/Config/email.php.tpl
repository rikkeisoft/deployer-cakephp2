<?php

class EmailConfig
{

    /**
     * Default email config
     * @var array
     */
    public $default = array(
        'transport'     => 'Smtp',
        'from'          => {{email.default.from}},
        'host'          => '{{email.default.host}}',
        'port'          => {{email.default.port}},
        'timeout'       => 30,
        'username'      => {{email.default.user}},
        'password'      => {{email.default.pass}},
        'client'        => null,
        'log'           => true,
        'charset'       => 'utf-8',
        'headerCharset' => 'utf-8',
        'emailFormat'   => 'text',
    );

}
