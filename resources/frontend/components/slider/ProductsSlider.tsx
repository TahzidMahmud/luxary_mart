import { SwiperProps } from 'swiper/react';
import ThemeSlider from './ThemeSlider';

type BreakPoints = 320 | 400 | 640 | 768 | 1024 | 1280;
type BreakPointsObj = {
    [key in BreakPoints]?: {
        slidesPerView?: number | 'auto';
        spaceBetween?: number;
    };
};

interface Props extends Omit<SwiperProps, 'breakpoints'> {
    breakpoints?: BreakPointsObj;
    withDefaultBreakpoints?: boolean;
}

const ProductsSlider = ({
    slidesPerView,
    spaceBetween,
    breakpoints,
    withDefaultBreakpoints = false,
    children,
    className,
    ...rest
}: Props) => {
    const defaultBreakpoints = {
        320: {
            slidesPerView: 2,
            spaceBetween: 5,
        },
        640: {
            slidesPerView: 3,
        },
        1024: {
            slidesPerView: 4,
        },
        1280: {
            slidesPerView: 5,
        },
    };

    return (
        <ThemeSlider
            containerClass={className}
            slidesPerView={slidesPerView || 1}
            spaceBetween={spaceBetween || 10}
            breakpoints={{
                ...defaultBreakpoints,
                ...(breakpoints as any),
            }}
            {...rest}
        >
            {children}
        </ThemeSlider>
    );
};

export default ProductsSlider;
