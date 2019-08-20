<?php

namespace Mautic\EmailBundle\BeeEditor;

interface BeeFreeAdapter
{
    public function setClientID($id);

    public function setClientSecret($secret);

    public function getCredentials();
}
