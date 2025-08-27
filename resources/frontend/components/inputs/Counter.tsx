import { useEffect, useState } from 'react';
import { FaMinus, FaPlus } from 'react-icons/fa6';
import { cn } from '../../utils/cn';
import { paddedNumber } from '../../utils/numberFormatter';

export type TCounterOrder = 'decrement' | 'count' | 'increment';

interface Props {
    value: number;
    max?: number;
    min?: number;
    pad?: number;
    size?: 'sm' | 'md' | 'lg';
    order?: [TCounterOrder, TCounterOrder, TCounterOrder];
    orientation?: 'horizontal' | 'vertical';
    classNames?: {
        container?: string;
        button?: string;
    };

    onChange?: (value: number) => void;
    onDecrement?: () => void;
    onIncrement?: () => void;
}

const Counter = ({
    value,
    max,
    min = 1,
    pad = 2,
    size = 'md',
    orientation = 'vertical',
    order = ['decrement', 'count', 'increment'],
    classNames,
    onChange,
    onDecrement,
    onIncrement,
}: Props) => {
    const [count, setCount] = useState(0);

    useEffect(() => {
        setCount(value);
    }, [value]);

    const handleIncrement = () => {
        if (typeof max === 'number' && count >= max) {
            return;
        }
        setCount(count + 1);
        onIncrement?.();
        onChange?.(count + 1);
    };

    const handleDecrement = () => {
        if (count <= min) {
            return;
        }
        setCount(count - 1);
        onDecrement?.();
        onChange?.(count - 1);
    };

    const sizeClasses = {
        sm: 'w-5 aspect-square',
        md: 'w-5 sm:w-[38px] aspect-square',
        lg: 'w-5 xs:w-[38px] sm:w-[50px] aspect-square',
    };

    const buttonClasses = cn(
        `rounded flex items-center justify-center border border-zinc-100 text-base text-theme-secondary-light ${sizeClasses[size]}`,
        classNames?.button,
    );

    return (
        <div
            className={cn(
                `flex items-center text-center gap-1 sm:gap-3 ${
                    orientation === 'horizontal' ? '' : 'flex-col'
                }`,
                classNames?.container,
            )}
        >
            {order.map((el) => {
                switch (el) {
                    case 'decrement':
                        return (
                            <button
                                className={buttonClasses}
                                onClick={handleDecrement}
                                key={el}
                            >
                                <FaMinus />
                            </button>
                        );
                    case 'increment':
                        return (
                            <button
                                className={buttonClasses}
                                onClick={handleIncrement}
                                key={el}
                            >
                                <FaPlus />
                            </button>
                        );
                    case 'count':
                        return (
                            <span className="text-xs text-neutral-500" key={el}>
                                {paddedNumber(count, pad)}
                            </span>
                        );
                    default:
                        return null;
                }
            })}
        </div>
    );
};

export default Counter;
