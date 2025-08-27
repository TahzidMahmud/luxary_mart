import { Variant } from '../../types';
import { cn } from '../../utils/cn';

export const labelVariantClasses: Record<Variant, string> = {
    primary: 'bg-theme-primary text-white',
    secondary: 'bg-theme-secondary text-white',
    success: 'bg-theme-green text-white',
    warning: 'bg-theme-orange text-white',
    danger: 'bg-theme-alert text-white',
    info: 'bg-blue-500 text-white',
    light: 'bg-white text-zinc-500',
    dark: 'bg-zinc-800 text-white',
    link: 'text-zinc-700',
    'no-color': 'text-white',
};

interface Props {
    label: { text: string; variant: Variant };
    bgColor?: string;
    textColor?: string;
    className?: string;
}

const ThemeLabel = ({ label, bgColor, textColor, className }: Props) => {
    return (
        <div
            className={cn(
                `text-[11px] py-0.5 px-2 font-semibold rounded-[3px] inline-block uppercase`,
                labelVariantClasses[label.variant || 'warning'],
                className,
            )}
            style={{
                backgroundColor: bgColor || '',
                color: textColor || '',
            }}
        >
            {label.text}
        </div>
    );
};

export default ThemeLabel;
