import { ReactNode } from 'react';
import { PiArrowRightLight } from 'react-icons/pi';
import { cn } from '../../utils/cn';
import Button from '../buttons/Button';

interface Props extends React.HTMLAttributes<HTMLDivElement> {
    title: string;
    icon?: ReactNode;
    linkText?: string;
    link?: string;

    onClick?: () => void;
    linkClassName?: string;
}

const SectionTitle = ({
    title,
    icon,
    link,
    linkText,
    onClick,
    className,
    linkClassName,
    ...rest
}: Props) => {
    return (
        <div
            className={cn(
                `flex items-center justify-between gap-3 pb-4 border-b-2 border-theme-primary relative mb-5`,
                className,
            )}
            {...rest}
        >
            <div className="flex items-center gap-1 sm:gap-3 relative z-[1] bg-white pr-4">
                {icon && (
                    <span className="text-xs sm:text-sm md:text-xl text-theme-primary">
                        {icon}
                    </span>
                )}
                <h2 className="arm-h2 capitalize">
                    {title.replaceAll(/[_-]/g, ' ')}
                </h2>
            </div>

            {link && linkText ? (
                <Button
                    as="link"
                    to={link}
                    className={`relative z-[1] ${linkClassName}`}
                >
                    {linkText}
                    <span className="text-xs sm:text-sm md:text-2xl">
                        <PiArrowRightLight />
                    </span>
                </Button>
            ) : linkText && onClick ? (
                <Button
                    onClick={onClick}
                    className={`relative z-[1] ${linkClassName}`}
                >
                    {linkText}
                    <span className="text-xs sm:text-sm md:text-2xl">
                        <PiArrowRightLight />
                    </span>
                </Button>
            ) : null}
        </div>
    );
};

export default SectionTitle;
