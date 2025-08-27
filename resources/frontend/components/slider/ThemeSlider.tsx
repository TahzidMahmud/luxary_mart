import { useEffect, useRef } from 'react';
import { FaAngleRight } from 'react-icons/fa';
import { FaAngleLeft } from 'react-icons/fa6';
import { Swiper, SwiperProps, SwiperRef } from 'swiper/react';
import { cn } from '../../utils/cn';

interface Props extends SwiperProps {
    arrows?: boolean;
    arrowClassNames?: {
        prev?: string;
        next?: string;
    };
    containerClass?: string;
}

const ThemeSlider = ({
    containerClass,
    className,
    arrows = true,
    arrowClassNames,
    children,
    ...rest
}: Props) => {
    const options = rest;

    const nextEl = useRef<HTMLButtonElement>(null);
    const prevEl = useRef<HTMLButtonElement>(null);
    const sliderEl = useRef<SwiperRef>(null);

    const disablePrevAndNextArrow = () => {
        if (sliderEl.current?.swiper.isBeginning) {
            prevEl.current?.classList.add('opacity-60', 'cursor-not-allowed');
        } else {
            prevEl.current?.classList.remove(
                'opacity-60',
                'cursor-not-allowed',
            );
        }

        if (sliderEl.current?.swiper.isEnd) {
            nextEl.current?.classList.add('opacity-60', 'cursor-not-allowed');
        } else {
            nextEl.current?.classList.remove(
                'opacity-60',
                'cursor-not-allowed',
            );
        }
    };

    useEffect(() => {
        sliderEl.current?.swiper.on('slideChange', disablePrevAndNextArrow);
    }, [sliderEl.current?.swiper]);

    const handleNext = () => {
        sliderEl.current?.swiper.slideNext();
    };

    const handlePrev = () => {
        sliderEl.current?.swiper.slidePrev();
    };

    const commonButtonClasses =
        'w-5 md:w-7 h-5 md:h-7 absolute top-1/2 -translate-y-1/2 z-[1] flex items-center justify-center rounded-full bg-white text-theme-secondary-light border border-theme-secondary-light hover:bg-theme-secondary-light hover:text-white';

    return (
        <div className={cn('relative min-w-0 min-h-0', containerClass)}>
            <Swiper
                className={`theme-slider ${className}`}
                ref={sliderEl}
                onInit={disablePrevAndNextArrow}
                {...options}
            >
                {children}
            </Swiper>

            {arrows && (
                <>
                    <button
                        className={cn(
                            commonButtonClasses,
                            `left-0 -translate-x-1/2`,
                            arrowClassNames?.prev,
                        )}
                        onClick={handlePrev}
                        ref={prevEl}
                    >
                        <FaAngleLeft />
                    </button>
                    <button
                        className={cn(
                            commonButtonClasses,
                            `right-0 translate-x-1/2`,
                            arrowClassNames?.next,
                        )}
                        onClick={handleNext}
                        ref={nextEl}
                    >
                        <FaAngleRight />
                    </button>
                </>
            )}
        </div>
    );
};

export default ThemeSlider;
