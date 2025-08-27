import ReactCountdown, { CountdownRendererFn } from 'react-countdown';
import { cn } from '../utils/cn';

// Renderer callback with condition
const renderer: CountdownRendererFn = ({
    days,
    hours,
    minutes,
    seconds,
    completed,
}) => {
    if (completed) {
        return null; // Don't render anything when the countdown is completed
    } else {
        return (
            <div className="flex items-center gap-[2px]">
                <div className="h-7 xs:h-9 min-w-[28px] xs:min-w-[36px] px-1 flex items-center justify-center bg-red-500 text-white">
                    {days}d
                </div>
                <span className="text-orange-500 font-bold">:</span>
                <div className="h-7 xs:h-9 min-w-[28px] xs:min-w-[36px] px-1 flex items-center justify-center bg-red-500 text-white">
                    {hours}h
                </div>
                <span className="text-orange-500 font-bold">:</span>
                <div className="h-7 xs:h-9 min-w-[28px] xs:min-w-[36px] px-1 flex items-center justify-center bg-red-500 text-white">
                    {minutes}m
                </div>
                <span className="text-orange-500 font-bold">:</span>
                <div className="h-7 xs:h-9 min-w-[28px] xs:min-w-[36px] px-1 flex items-center justify-center bg-red-500 text-white">
                    {seconds}s
                </div>
            </div>
        );
    }
};

interface Props {
    date?: Date | string | number | null;
    label?: string;
    className?: string;
    classNames?: {
        container?: string;
        label?: string;
        countDown?: string;
    };
}

const CountDown = ({ label, className, classNames, date }: Props) => {
    if (!date) return null;

    if (!label) {
        return (
            <ReactCountdown
                className={classNames?.countDown}
                date={new Date(date)}
                renderer={renderer}
            />
        );
    }

    return (
        <div
            className={cn(
                'flex items-center gap-4 mt-4',
                className,
                classNames?.container,
            )}
        >
            <span className={cn('arm-h4 text-black', classNames?.label)}>
                {label}
            </span>
            <ReactCountdown
                className={classNames?.countDown}
                date={new Date(date)}
                renderer={renderer}
            />
        </div>
    );
};

export default CountDown;
