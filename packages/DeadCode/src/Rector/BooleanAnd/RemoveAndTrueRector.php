<?php declare(strict_types=1);

namespace Rector\DeadCode\Rector\BooleanAnd;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PHPStan\Type\Constant\ConstantBooleanType;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

final class RemoveAndTrueRector extends AbstractRector
{
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Remove and true that has no added value', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return true && 5 === 1;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return 5 === 1;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [BooleanAnd::class];
    }

    /**
     * @param BooleanAnd $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->isTrueOrBooleanAndTrues($node->left)) {
            return $node->right;
        }

        if ($this->isTrueOrBooleanAndTrues($node->right)) {
            return $node->left;
        }

        return null;
    }



    private function isTrueOrBooleanAndTrues(Node $node): bool
    {
        if ($this->isTrue($node)) {
            return true;
        }

        if ($node instanceof BooleanAnd) {
            if ($this->isTrueOrBooleanAndTrues($node->left) && $this->isTrueOrBooleanAndTrues($node->right)) {
                return true;
            }
        }

        return false;
    }
}
