import { DetailedHTMLProps, InputHTMLAttributes } from 'react';
import { Variant } from '../../types';
import { cn } from '../../utils/cn';

interface Props
    extends Omit<
        DetailedHTMLProps<
            InputHTMLAttributes<HTMLInputElement>,
            HTMLInputElement
        >,
        'color'
    > {
    name: string;
    label?: string;
    value: string | number;
    code?: string;
    image?: string;
    variant?: Variant;
}

const ColorCheckbox = ({
    name,
    label,
    value,
    code,
    image,
    checked,
    variant,
    ...rest
}: Props) => {
    return (
        <label
            title={String(label)}
            className={cn(
                `theme-checkbox-check options !h-[60px] !w-[60px] rounded-md overflow-hidden border`,
                {
                    [`theme-checkbox--${variant}`]: variant,
                    'border border-theme-primary ': checked,
                },
            )}
        >
            <input
                type="checkbox"
                id={name}
                className="theme-checkbox__input"
                name={name}
                value={value}
                checked={checked}
                {...rest}
            />

            {image ? (
                <img src={image} alt={label} className="w-full h-full" />
            ) : null}
        </label>
    );
};

export default ColorCheckbox;
