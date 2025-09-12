import { ButtonHTMLAttributes, DetailedHTMLProps } from 'react';
import { Link, LinkProps as ReactLinkProps } from 'react-router-dom';
import { Size, Variant } from '../../types';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import Spinner from '../loader/Spinner';

type ButtonProps = {
    as?: 'button';
} & DetailedHTMLProps<
    ButtonHTMLAttributes<HTMLButtonElement>,
    HTMLButtonElement
>;

type LinkProps = {
    as?: 'link';
} & ReactLinkProps;

type CommonProps = {
    variant?: Variant;
    isLoading?: boolean;
    size?: Size;
    arrow?: false | 'left' | 'right';
};

type Props = CommonProps & (ButtonProps | LinkProps);

const Button = (props: Props) => {
    const { isLoading } = props;

    const variantClasses = {
         outline: cn(
            'text-primary border border-secondary bg-gradient-to-r from-white from-[50%] to-secondary to-[50%] bg-[200%_auto] hover:bg-right hover:bg-right hover:text-white',
            {
                'pl-5 pr-3 md:pl-6 md:pr-5':
                    props.arrow === 'right' || props.arrow === undefined,
                'pl-3 pr-5 md:pl-5 md:pr-6': props.arrow === 'left',
                'px-5': props.arrow === false,
            },
        ),
        primary: `bg-theme-primary text-white hover:bg-theme-primary/90`,
        secondary: `bg-theme-secondary-light text-white hover:bg-theme-secondary`,
        success: `bg-theme-green text-white hover:bg-theme-green/70`,
        warning: `bg-theme-orange text-white hover:bg-theme-orange/70`,
        danger: `bg-theme-alert text-white hover:bg-theme-alert/70`,
        info: `bg-blue-500 text-white hover:bg-blue-500/70`,
        light: `bg-white text-zinc-500 hover:bg-zinc-100`,
        dark: `bg-zinc-800 text-white hover:bg-zinc-700`,
        link: `text-zinc-700 hover:bg-neutral-100`,
        'no-color': '',
    }[props.variant || 'primary'];

    const sizeClasses = {
        sm: `h-8 text-sm px-3`,
        md: `h-8 md:h-10 px-3`,
        lg: `h-10 md:h-12 px-4 md:px-6`,
        full: 'w-full grow',
    }[props.size || 'md'];

    const defaultClassName = `leading-none uppercase inline-flex justify-center items-center gap-2.5 rounded transition-all disabled:bg-gray-300 disabled:cursor-not-allowed disabled:text-white`;

    if (props.as === 'link') {
        const { className, children, ...rest } = props;

        return (
            <Link
                className={cn(
                    defaultClassName,
                    variantClasses,
                    sizeClasses,
                    className,
                )}
                {...rest}
            >
                {isLoading ? (
                    <span className="inline-flex items-center gap-2">
                        <Spinner /> {translate('Loading')}...
                    </span>
                ) : (
                    children
                )}
            </Link>
        );
    } else {
        const { className, isLoading, children, ...rest } =
            props as CommonProps & ButtonProps;
        return (
            <button
                className={cn(
                    defaultClassName,
                    variantClasses,
                    sizeClasses,
                    className,
                )}
                disabled={isLoading}
                {...rest}
            >
                {isLoading ? (
                    <span className="inline-flex items-center gap-2">
                        <Spinner /> {translate('Loading')}...
                    </span>
                ) : (
                    children
                )}
            </button>
        );
    }
};

export default Button;
