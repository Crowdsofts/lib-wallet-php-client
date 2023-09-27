<?php

namespace Paysera\WalletApi\Entity\Client;

class SearchFilter extends \Paysera\WalletApi\Entity\Search\Filter
{
    /**
     * @var string
     */
    private $projectId;

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     *
     * @return $this
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }
}
