<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CrisisResourceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrisisResourceRepository::class)]
#[ORM\Table(name: 'crisis_resources')]
class CrisisResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(name: 'country_code', referencedColumnName: 'code', nullable: false)]
    private Country $country;

    #[ORM\Column(type: 'string', length: 30)]
    private string $type;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $url;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $textNumber;

    #[ORM\Column(type: 'boolean')]
    private bool $is24h;

    #[ORM\Column(type: 'boolean')]
    private bool $isFree;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $noPoliceByDefault = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPrimary = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $languages;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $sortOrder = 0;

    public function __construct(
        Country $country,
        string $type,
        string $name,
        bool $is24h,
        bool $isFree,
        ?string $description = null,
        ?string $phone = null,
        ?string $url = null,
        ?string $textNumber = null,
        bool $noPoliceByDefault = false,
        bool $isPrimary = false,
        ?array $languages = null,
        int $sortOrder = 0,
    ) {
        $this->country = $country;
        $this->type = $type;
        $this->name = $name;
        $this->is24h = $is24h;
        $this->isFree = $isFree;
        $this->description = $description;
        $this->phone = $phone;
        $this->url = $url;
        $this->textNumber = $textNumber;
        $this->noPoliceByDefault = $noPoliceByDefault;
        $this->isPrimary = $isPrimary;
        $this->languages = $languages;
        $this->sortOrder = $sortOrder;
    }

    public function getId(): int { return $this->id; }
    public function getCountry(): Country { return $this->country; }
    public function getType(): string { return $this->type; }
    public function getName(): string { return $this->name; }
    public function getDescription(): ?string { return $this->description; }
    public function getPhone(): ?string { return $this->phone; }
    public function getUrl(): ?string { return $this->url; }
    public function getTextNumber(): ?string { return $this->textNumber; }
    public function is24h(): bool { return $this->is24h; }
    public function isFree(): bool { return $this->isFree; }
    public function isNoPoliceByDefault(): bool { return $this->noPoliceByDefault; }
    public function isPrimary(): bool { return $this->isPrimary; }
    public function getLanguages(): ?array { return $this->languages; }
    public function getSortOrder(): int { return $this->sortOrder; }
}
