<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(name: 'countries')]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 2)]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $nameEn;

    #[ORM\Column(type: 'string')]
    private string $nameLocal;

    #[ORM\Column(type: 'string', length: 10)]
    private string $primaryLanguage;

    #[ORM\Column(type: 'string', length: 5)]
    private string $flagEmoji;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $suicideIllegal = false;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $policeDispatchRisk = true;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: CrisisResource::class, fetch: 'EXTRA_LAZY')]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    private Collection $resources;

    public function __construct(
        string $code,
        string $nameEn,
        string $nameLocal,
        string $primaryLanguage,
        string $flagEmoji,
        bool $suicideIllegal = false,
        bool $policeDispatchRisk = true,
    ) {
        $this->code = $code;
        $this->nameEn = $nameEn;
        $this->nameLocal = $nameLocal;
        $this->primaryLanguage = $primaryLanguage;
        $this->flagEmoji = $flagEmoji;
        $this->suicideIllegal = $suicideIllegal;
        $this->policeDispatchRisk = $policeDispatchRisk;
        $this->resources = new ArrayCollection();
    }

    public function getCode(): string { return $this->code; }
    public function getNameEn(): string { return $this->nameEn; }
    public function getNameLocal(): string { return $this->nameLocal; }
    public function getPrimaryLanguage(): string { return $this->primaryLanguage; }
    public function getFlagEmoji(): string { return $this->flagEmoji; }
    public function isSuicideIllegal(): bool { return $this->suicideIllegal; }
    public function isPoliceDispatchRisk(): bool { return $this->policeDispatchRisk; }
    public function getResources(): Collection { return $this->resources; }
}
