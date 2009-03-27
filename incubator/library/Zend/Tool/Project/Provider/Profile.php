<?php

require_once 'Zend/Tool/Project/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Registry.php';

class Zend_Tool_Project_Provider_Profile extends Zend_Tool_Project_Provider_Abstract
{
    public function show()
    {
        $profile = $this->_loadProfile();

        if ($profile === false) {
            $this->_registry->getResponse()->appendContent('There is no profile located here.');
            return;
        }

        $profileIterator = $profile->getIterator();

        foreach ($profileIterator as $profileItem) {
            $this->_registry->getResponse()->appendContent(
                str_repeat('    ', $profileIterator->getDepth()) . $profileItem
            );
        }

        /*
        foreach ($profile->getTopResources() as $resource) {
            $response->appendContent($resource->getName() . PHP_EOL);
            $rii = new RecursiveIteratorIterator($resource, RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $item) {
                $response->appendContent(
                    str_repeat('  ', $rii->getDepth()+1) . $item->getName()
                );
            }

        }
        */
    }
}