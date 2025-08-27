import { Link, LinkProps as ReactLinkProps } from 'react-router-dom';
import { cn } from '../../utils/cn';
import Spinner from '../loader/Spinner';

type ButtonProps = {
    as?: 'button';
    isLoading?: boolean;
} & React.HTMLAttributes<HTMLButtonElement>;

type LinkProps = {
    as?: 'link';
} & ReactLinkProps;

type Props = ButtonProps | LinkProps;

const IconButton = (props: Props) => {
    const defaultClassName = `h-6 sm:h-8 w-6 sm:w-8 inline-flex justify-center items-center rounded transition-all bg-white text-zinc-500 text-xs sm:text-sm border border-theme-primary-14 hover:bg-theme-secondary-light hover:text-white`;

    if (props.as === 'link') {
        const { className, children, ...rest } = props;
        return (
            <Link className={cn(defaultClassName, className)} {...rest}>
                {children}
            </Link>
        );
    } else {
        const { isLoading, ...rest } = props as ButtonProps;
        return (
            <button {...rest} className={cn(defaultClassName, props.className)}>
                {isLoading ? (
                    <span className="inline-flex items-center gap-2">
                        <Spinner />
                    </span>
                ) : (
                    props.children
                )}
            </button>
        );
    }
};

export default IconButton;
