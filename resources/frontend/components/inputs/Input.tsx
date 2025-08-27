import { useEffect, useState } from 'react';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import Checkbox from './Checkbox';

interface Props extends React.InputHTMLAttributes<HTMLInputElement> {
    error?: string;
    touched?: boolean;
    toggleBtn?: boolean;
}

const Input = ({
    name,
    type,
    className,
    error,
    touched,
    toggleBtn = true,
    ...rest
}: Props) => {
    const [inputType, setInputType] = useState(type);

    useEffect(() => {
        setInputType(type);
    }, [type]);

    return (
        <>
            <input
                type={inputType}
                name={name}
                className={cn(
                    `w-full border border-theme-primary-14 rounded-md h-8 sm:h-10 px-[15px] focus:border-theme-secondary-light placeholder:text-neutral-400`,
                    className,
                )}
                {...rest}
            />

            {error && touched && (
                <span className="text-sm text-theme-alert block">
                    {translate(error)}
                </span>
            )}

            {type === 'password' && toggleBtn && (
                <Checkbox
                    label={translate('Show Password')}
                    className="mt-2"
                    labelClassName="text-xs"
                    onChange={() => {
                        setInputType(
                            inputType === 'password' ? 'text' : 'password',
                        );
                    }}
                />
            )}
        </>
    );
};

export default Input;
