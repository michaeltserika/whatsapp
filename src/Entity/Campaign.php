<?php

namespace App\Entity;

use App\Repository\CampaignRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignRepository::class)]
class Campaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $email_list = [];

    #[ORM\Column(type: Types::ARRAY)]
    private array $template = [];

    #[ORM\Column(length: 255)]
    private ?string $smtp_details = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $create_on = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEmailList(): array
    {
        return $this->email_list;
    }

    public function setEmailList(array $email_list): static
    {
        $this->email_list = $email_list;

        return $this;
    }

    public function getTemplate(): array
    {
        return $this->template;
    }

    public function setTemplate(array $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getSmtpDetails(): ?int
    {
        return $this->smtp_details;
    }

    public function setSmtpDetails(string $smtp): static
    {
        $this->smtp_details = $smtp;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreateOnAsString(string $format): string
    {
        return $this->create_on->format($format);
    }

    public function getCreateOn(?string $format = ''): ?\DateTimeInterface
    {
        return $this->create_on;
    }

    public function setCreateOn(?\DateTimeInterface $create_on): static
    {
        $this->create_on = $create_on;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(?string $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
    
}
