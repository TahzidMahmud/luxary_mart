import React, { ReactNode } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import { cn } from '../../../react/utils/cn';
import { useAppContext } from '../../Context';

interface Props {
    children: ReactNode | ReactNode[];
    size?: 'sm' | 'md' | 'lg';
    isOpen: boolean;
    classNames?: {
        wrapper?: string;
        button?: string;
    };
}

const PopupWrapper = ({ children, size = 'sm', classNames, isOpen }: Props) => {
    const { closePopup } = useAppContext();

    const sizeClasses = {
        sm: 'max-w-[400px]',
        md: 'max-w-[600px]',
        lg: 'max-w-[800px]',
    }[size];

    return (
        <div
            className={cn(
                'bg-background rounded-md fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 min-h-[300px] max-h-screen w-full overflow-y-auto transition-all z-[3]',
                sizeClasses,
                {
                    '-translate-y-[calc(50%-10px)] opacity-0 invisible':
                        !isOpen,
                },
                classNames?.wrapper,
            )}
        >
            <button
                className={cn(
                    'absolute right-3 top-2 text-lg',
                    classNames?.button,
                )}
                onClick={closePopup}
            >
                <LiaTimesSolid />
            </button>
            {children}
        </div>
    );
};

export default PopupWrapper;
