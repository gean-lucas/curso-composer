<?php

declare(strict_types=1);

namespace Phan\Language\Type;

use Phan\CodeBase;
use Phan\Language\Context;
use Phan\Language\Type;

/**
 * Phan's representation for `callable`
 *
 * @see CallableDeclarationType for Phan's representation of `callable(MyClass):MyOtherClass`
 * @phan-pure
 */
final class CallableType extends NativeType implements CallableInterface
{
    use NativeTypeTrait;

    /** @phan-override */
    public const NAME = 'callable';

    /**
     * @return bool
     * True if this type is a callable or a Closure.
     * @unused-param $code_base
     */
    public function isCallable(CodeBase $code_base): bool
    {
        return true;
    }

    protected function canCastToNonNullableType(Type $type, CodeBase $code_base): bool
    {
        // CallableDeclarationType is not a native type, we check separately here
        return parent::canCastToNonNullableType($type, $code_base) || $type instanceof CallableDeclarationType;
    }

    protected function canCastToNonNullableTypeWithoutConfig(Type $type, CodeBase $code_base): bool
    {
        // CallableDeclarationType is not a native type, we check separately here
        return parent::canCastToNonNullableTypeWithoutConfig($type, $code_base) || $type instanceof CallableDeclarationType;
    }

    /**
     * Returns true if this contains a type that is definitely non-callable
     * e.g. returns true for false, array, int
     *      returns false for callable, string, array, object, iterable, T, etc.
     * @unused-param $code_base
     */
    public function isDefiniteNonCallableType(CodeBase $code_base): bool
    {
        return false;
    }

    public function isPossiblyObject(): bool
    {
        return true;  // callable-object, Closure, etc. are objects
    }

    public function asObjectType(): ?Type
    {
        return CallableObjectType::instance(false);
    }

    /**
     * Convert this to a subtype that satisfies is_array(), or returns null
     * @see UnionType::arrayTypesStrictCast
     */
    public function asArrayType(): ?Type
    {
        return CallableArrayType::instance(false);
    }

    public function asScalarType(): ?Type
    {
        return CallableStringType::instance(false);
    }

    public function canCastToDeclaredType(CodeBase $code_base, Context $context, Type $other): bool
    {
        if ($other instanceof IterableType) {
            return !$other->isDefiniteNonCallableType($code_base);
        }
        // TODO: More specific.
        return $other instanceof StringType
            || $other instanceof ObjectType
            || $other instanceof IterableType
            || parent::canCastToDeclaredType($code_base, $context, $other);
    }

    /** @unused-param $code_base */
    public function isSubtypeOfNonNullableType(Type $type, CodeBase $code_base): bool
    {
        return $type instanceof CallableType || $type instanceof MixedType;
    }
}
