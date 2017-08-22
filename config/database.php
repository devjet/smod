<?php

ORM::configure('pgsql:host=' . $settings['settings']['db']['host'] . ';dbname=' . $settings['settings']['db']['database']);
ORM::configure('username', $settings['settings']['db']['username']);
ORM::configure('password', $settings['settings']['db']['password']);
ORM::configure('logging', $settings['settings']['db']['logging']);

