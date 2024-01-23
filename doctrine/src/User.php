<?php
// src/User.php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'wp_users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', nullable: false, options: ['unsigned' => true] )]
    public int|null $ID = null;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length:250)]
    public string $display_name;

    /** @var Collection<int, Request> */
    #[ORM\OneToMany(targetEntity: Request::class, mappedBy: 'user')]
    private Collection $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
    }

    public function assignToRequests(Request $request): void
    {
        $this->requests[] = $request;
    }
}
