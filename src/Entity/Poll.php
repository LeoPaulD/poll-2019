<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PollRepository")
 */
class Poll
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PollOption", mappedBy="poll")
     */
    private $pollOptions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="polls")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PollVote", mappedBy="poll", orphanRemoval=true)
     */
    private $pollVotes;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
        $this->pollOptions = new ArrayCollection();
        $this->pollVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection|PollOption[]
     */
    public function getPollOptions(): Collection
    {
        return $this->pollOptions;
    }

    public function addPollOption(PollOption $pollOption): self
    {
        if (!$this->pollOptions->contains($pollOption)) {
            $this->pollOptions[] = $pollOption;
            $pollOption->setPoll($this);
        }

        return $this;
    }

    public function removePollOption(PollOption $pollOption): self
    {
        if ($this->pollOptions->contains($pollOption)) {
            $this->pollOptions->removeElement($pollOption);
            // set the owning side to null (unless already changed)
            if ($pollOption->getPoll() === $this) {
                $pollOption->setPoll(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    // /**
    //  * @param User $user
    //  * @return pollVote | null
    //  */
    // public function getvoteFromUser(User $user)
    // {
    //     foreach($this->pollVotes as $pollVote)
    //     {
    //         if($poll->getUser()===$user) 
    //         return $pollVote;
    //     }
    //     return null;
    // }
    /**
     * @return Collection|PollVote[]
     */
    public function getPollVotes(): Collection
    {
        return $this->pollVotes;
    }

    public function addPollVote(PollVote $pollVote): self
    {
        if (!$this->pollVotes->contains($pollVote)) {
            $this->pollVotes[] = $pollVote;
            $pollVote->setPoll($this);
        }

        return $this;
    }

    public function removePollVote(PollVote $pollVote): self
    {
        if ($this->pollVotes->contains($pollVote)) {
            $this->pollVotes->removeElement($pollVote);
            // set the owning side to null (unless already changed)
            if ($pollVote->getPoll() === $this) {
                $pollVote->setPoll(null);
            }
        }

        return $this;
    }
}
