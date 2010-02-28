<?php

namespace zend\cache\plugin;
use \zend\cache\adapter\AdapterInterface as AdapterInterface;

interface PluginInterface extends AdapterInterface
{

    public function getAdapter();

    public function setAdapter(AdapterInterface $innerAdapter);

}
