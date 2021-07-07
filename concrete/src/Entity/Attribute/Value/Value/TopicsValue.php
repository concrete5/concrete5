<?php
namespace Concrete\Core\Entity\Attribute\Value\Value;

use Concrete\Core\Tree\Node\Node;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atTopic")
 */
class TopicsValue extends AbstractValue
{
    /**
     * @ORM\OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Value\Value\SelectedTopic", mappedBy="value", cascade={"all"})
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $topics;

    /**
     * TopicsValue constructor.
     *
     * @param $topics
     */
    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    public function getSelectedTopics()
    {
        return $this->topics;
    }

    public function setSelectedTopics($topics)
    {
        $this->topics = $topics;
    }

    public function getSelectedTopicNodes()
    {
        $topics = array();
        foreach($this->topics as $selectedTopic) {
            $node = Node::getByID($selectedTopic->getTreeNodeID());
            if (is_object($node)) {
                $topics[] = $node;
            }
        }
        return $topics;
    }

    public function getValue()
    {
        return $this->getSelectedTopicNodes();
    }

    public function __toString()
    {
        $list = $this->getSelectedTopicNodes();
        $topics = array();
        foreach ($list as $topic) {
            $topics[] = $topic->getTreeNodeDisplayName();
        }
        return implode(', ', $topics);
    }

    public function __clone()
    {
        $clonedSelectedTopics = new ArrayCollection();
        foreach ($this->getSelectedTopics() as $selectedTopic) {
            /** @var SelectedTopic $clonedSelectedTopic */
            $clonedSelectedTopic = clone $selectedTopic;
            $clonedSelectedTopic->setAttributeValue($this);
            $clonedSelectedTopics->add($clonedSelectedTopic);
        }
        $this->setSelectedTopics($clonedSelectedTopics);
    }
}
