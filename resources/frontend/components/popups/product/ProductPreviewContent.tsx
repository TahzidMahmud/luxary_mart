import { useEffect, useRef, useState } from 'react';
import toast from 'react-hot-toast';
import { FaHeart } from 'react-icons/fa6';
import { FiChevronLeft, FiChevronRight } from 'react-icons/fi';
import { SideBySideMagnifier } from 'react-image-magnifiers';
import { useDispatch } from 'react-redux';
import { Link, useNavigate } from 'react-router-dom';
import { Thumbs } from 'swiper/modules';
import { Swiper, SwiperRef, SwiperSlide } from 'swiper/react';
import NotFound from '../../../pages/NotFound';
import {
    useAddToCartMutation,
    useAddToWishListMutation,
    useGetProductDetailsQuery,
} from '../../../store/features/api/productApi';
import { useAuth } from '../../../store/features/auth/authSlice';
import { togglePopup } from '../../../store/features/popup/popupSlice';
import { VariationCombination } from '../../../types/product';
import { isProductAvailable } from '../../../utils/checkStock';
import { cn } from '../../../utils/cn';
import { currencyFormatter } from '../../../utils/numberFormatter';
import { translate } from '../../../utils/translate';
import CountDown from '../../CountDown';
import Image from '../../Image';
import Button from '../../buttons/Button';
import IconButton from '../../buttons/IconButton';
import ThemeLabel from '../../card/ThemeLabel';
import ColorCheckbox from '../../inputs/ColorCheckbox';
import Counter from '../../inputs/Counter';
import ThemeRating from '../../reviews/ThemeRating';
import {
    ProductPreviewSkeleton,
    ProductPreviewSkeletonMobile,
} from '../../skeletons/from-svg';

interface ISelectedVariationCombinations {
    [key: number]: VariationCombination['values'][0] | null;
}

interface Props extends React.HTMLAttributes<HTMLDivElement> {
    slug: string;
    stickyButtons?: boolean;
}

const generateVariationCode = (
    variationCombinations: ISelectedVariationCombinations,
) => {
    let variationCode = '';
    Object.values(variationCombinations).forEach((value) => {
        if (value) variationCode += value.matchVariationCode + '/';
    });
    return variationCode;
};

const ProductPreviewContent = ({
    slug,
    stickyButtons = true,
    className,
    ...rest
}: Props) => {
    const navigate = useNavigate();
    const dispatch = useDispatch();

    const { user, userLocation, isLoading, guestUserId, carts } = useAuth();

    const { data: product, isLoading: loadingProduct } =
        useGetProductDetailsQuery(slug!);
    const [addToWishList] = useAddToWishListMutation();
    const [addToCart, { isLoading: addingToCart }] = useAddToCartMutation();

    const previewSliderRef = useRef<SwiperRef>(null);
    const thumbSliderRef = useRef<SwiperRef>(null);

    const [isBuyNowLoading, setIsBuyNowLoading] = useState(false);
    const [cartCount, setCartCount] = useState(1);
    const [selectedVariationCombinations, setSelectedVariationCombinations] =
        useState<ISelectedVariationCombinations>({});
    const [thumbsSwiper, setThumbsSwiper] = useState<
        SwiperRef['swiper'] | null
    >(null);

    // concat all images
    let images: string[] = [];
    if (product) {
        product.variations?.forEach((variation) => {
            if (variation.image) {
                images.push(variation.image);
            }
        });
        images = [...images, ...product.images];
    }

    // if window size is less then 1024px (max-lg)
    // add 136px padding to the bottom of the page to adjust the fixed buy now button
    useEffect(() => {
        if (window.innerWidth > 1024) return;

        const body = document.body;
        body.style.paddingBottom = '136px';

        return () => {
            body.style.paddingBottom = '';
        };
    }, []);

    // set default selected variations
    useEffect(() => {
        if (!product) return;

        setSelectedVariationCombinations((prev) => {
            const newSelectedVariations = { ...prev };

            product.variationCombinations.forEach((variation) => {
                newSelectedVariations[variation.id] = variation.values[0];
            });

            return newSelectedVariations;
        });
    }, [product]);

    // select variation image whenever selected variation combination changes
    useEffect(() => {
        if (!product) return;

        // generate the variation code from selected variations
        const selectedVariationCode = generateVariationCode(
            selectedVariationCombinations,
        );

        // find the selected variation using the generated variation code
        const selectedVariation = product.variations.find((variation) => {
            return variation.code === selectedVariationCode;
        });

        if (!selectedVariation?.image) return;

        const imageIndex = images.findIndex(
            (image) => image === selectedVariation?.image,
        );

        previewSliderRef.current?.swiper.slideTo(imageIndex);
    }, [selectedVariationCombinations, product]);

    if (loadingProduct) {
        return (
            <>
                <ProductPreviewSkeleton className="mx-auto max-md:hidden" />
                <div className="p-4 md:hidden">
                    <ProductPreviewSkeletonMobile className=" w-full" />
                </div>
            </>
        );
    }

    if (!product) {
        return <NotFound />;
    }

    // generate selected variation code
    let selectedVariationCode = generateVariationCode(
        selectedVariationCombinations,
    );

    // find the variation from selected combination
    // if `hasVariation` is 0, then select 1st variation
    // else find the variation using the generated variation code
    const selectedVariation = !product.hasVariation
        ? product.variations[0]
        : product.variations.find((variation) => {
              return variation.code === selectedVariationCode;
          });

    // find the quantity already added to cart of the selected variation
    const productCartQuantity = carts.find(
        (cartProduct) => cartProduct.variation.id === selectedVariation?.id,
    );

    const maximumAddableQuantity =
        (selectedVariation?.stocks[0]?.stockQty || 1) -
        (productCartQuantity?.qty || 0);

    const handleAddToCart = async () => {
        // check user location is set or not
        if (!userLocation?.area?.zone_id) {
            dispatch(togglePopup('user-location'));
            return;
        }

        try {
            let productVariationId = selectedVariation?.id;

            // if `hasVariation` is 0, then select 1st variation and add to cart
            // if `hasVariation` is grater then 0, then check if all variations are selected or not
            // if all variations are not selected,  then show error. Example: Please select color and size
            // else add to cart
            if (product.hasVariation === 0) {
                productVariationId = product.variations[0].id;
            }
            if (!productVariationId) {
                // find which variations are not selected
                const notSelectedVariations = product.variationCombinations
                    .map((variation) => {
                        if (!selectedVariationCombinations[variation.id]) {
                            return variation.name;
                        }
                        return null;
                    })
                    .filter((item) => item !== null);

                toast.error(
                    `Please select ${notSelectedVariations.join(', ')}`,
                );
                return;
            }

            // check if the selected variation is in stock or not
            if (!isProductAvailable(selectedVariation!, carts)) {
                return;
            }

            await addToCart({
                productVariationId,
                qty: cartCount,
                warehouseId: selectedVariation?.stocks[0].warehouseId!,
                guestUserId,
            }).unwrap();

            setCartCount(1);
            toast.success(translate('Product added to cart'));
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    // add to cart and redirect to checkout page
    const handleBuyNow = async () => {
        // check user location is set or not
        if (!userLocation?.area?.zone_id) {
            dispatch(togglePopup('user-location'));
            return;
        }

        // check if the selected variation is in stock or not
        if (!isProductAvailable(selectedVariation!, carts)) {
            return;
        }

        setIsBuyNowLoading(true);

        await handleAddToCart();

        setIsBuyNowLoading(false);
        navigate('/cart');
    };

    const handleAddToWishList = async () => {
        if (isLoading || !user) {
            dispatch(togglePopup('signin'));
            return;
        }

        try {
            await addToWishList(product.id).unwrap();
            toast.success(translate('Added to wishlist'));
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    const handleSelectVariation = ({
        variationId,
        variationValue,
        checked,
    }: any) => {
        if (!checked) {
            setSelectedVariationCombinations((prev) => ({
                ...prev,
                [variationId]: null,
            }));
            return;
        }

        setSelectedVariationCombinations((prev) => ({
            ...prev,
            [variationId]: variationValue,
        }));
    };

    const handlePrev = () => {
        if (!thumbSliderRef.current) return;
        thumbSliderRef.current.swiper.slidePrev();
    };

    const handleNext = () => {
        if (!thumbSliderRef.current) return;
        thumbSliderRef.current.swiper.slideNext();
    };

    return (
        <div className={`grid sm:grid-cols-9 ${className}`} {...rest}>
            <div className="sm:col-span-4 md:col-span-5">
                <div className="relative border border-theme-primary-14 rounded-md aspect-square">
                    <div className="absolute top-5 left-5 flex flex-col items-start gap-2 z-[2]">
                        {product.stockQty < 1 ? (
                            <ThemeLabel
                                label={{
                                    text: translate('Out of stock'),
                                    variant: 'no-color',
                                }}
                                bgColor={'#a6a6a6'}
                                textColor={'#fff'}
                            />
                        ) : null}

                        {product.badges.map((item) => (
                            <ThemeLabel
                                label={{
                                    text: item.name,
                                    variant: 'no-color',
                                }}
                                bgColor={item.bgColor}
                                textColor={item.textColor}
                                key={item.id}
                            />
                        ))}
                    </div>

                    <Swiper
                        modules={[Thumbs]}
                        slidesPerView={1}
                        autoHeight={true}
                        updateOnWindowResize={true}
                        mousewheel={true}
                        className="swiper-main product-preview-slider"
                        ref={previewSliderRef}
                        thumbs={{
                            swiper:
                                thumbsSwiper && !thumbsSwiper.destroyed
                                    ? thumbsSwiper
                                    : null,
                        }}
                    >
                        {images.map((image, index) => (
                            <SwiperSlide key={index}>
                                <SideBySideMagnifier
                                    imageSrc={image}
                                    className="[&_img]:max-w-none"
                                    alwaysInPlace={true}
                                />
                            </SwiperSlide>
                        ))}
                    </Swiper>
                </div>

                <div className="relative mt-2.5">
                    <button
                        className="absolute top-1/2 -translate-y-1/2 left-0 z-[1] inline-flex items-center justify-center text-lg text-stone-300 rounded-md border border-zinc-100 h-[38px] aspect-square hover:text-theme-secondary-light hover:border-theme-secondary-light"
                        onClick={handlePrev}
                    >
                        <FiChevronLeft />
                    </button>

                    <div className="md:px-14">
                        <Swiper
                            modules={[Thumbs]}
                            className="product-details__thumbs"
                            spaceBetween={10}
                            slidesPerView={3}
                            ref={thumbSliderRef}
                            watchSlidesProgress={true}
                            onSwiper={setThumbsSwiper}
                        >
                            {images.map((image, index) => (
                                <SwiperSlide key={index}>
                                    <div className="border border-zinc-100 rounded-md overflow-hidden">
                                        <Image
                                            src={image}
                                            alt=""
                                            // className="max-xs:w-[50px] max-sm:max-w-[100px]"
                                        />
                                    </div>
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    </div>

                    <button
                        className="absolute top-1/2 -translate-y-1/2 right-0 z-[1] inline-flex items-center justify-center text-lg text-stone-300 rounded-md border border-zinc-100 h-[38px] aspect-square hover:text-theme-secondary-light hover:border-theme-secondary-light"
                        onClick={handleNext}
                    >
                        <FiChevronRight />
                    </button>
                </div>
            </div>
            <div className="sm:col-span-5 md:col-span-4">
                <a href={`#customer-review`}>
                    <ThemeRating
                        readOnly
                        rating={product.rating.average}
                        totalReviews={product.rating.total}
                        className="mb-2.5"
                    />
                </a>
                <h1 className="text-xl font-public-sans">{product.name}</h1>
                <hr className="mt-2.5 mb-6" />
                {product.variationCombinations.map((variation) => (
                    <div
                        className="flex items-center gap-2 mb-6"
                        key={variation.id}
                    >
                        <span className="min-w-[73px] uppercase font-medium">
                            {variation.name}
                        </span>
                        <div className="flex flex-wrap gap-1">
                            {variation.values.map((item) => {
                                const itemSelected =
                                    selectedVariationCombinations[variation.id]
                                        ?.id === item.id;

                                const onChange = () =>
                                    handleSelectVariation({
                                        variationId: variation.id,
                                        variationValue: item,
                                        checked: !itemSelected,
                                    });

                                if (variation.id === 1) {
                                    // it's color variation
                                    return (
                                        <ColorCheckbox
                                            key={item.id}
                                            name="variationValueIds"
                                            label={item.name}
                                            value={item.id}
                                            image={item.variationImage}
                                            checked={itemSelected}
                                            onChange={onChange}
                                        />
                                    );
                                }

                                return (
                                    <button
                                        className={`h-9 min-w-9 px-2 rounded inline-flex items-center justify-center text-sm text-neutral-400 border ${
                                            itemSelected
                                                ? 'border-theme-secondary-light bg-theme-secondary-light text-white'
                                                : 'border-zinc-100'
                                        }`}
                                        onClick={onChange}
                                        key={item.id}
                                    >
                                        {item.name}
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                ))}
                <div className="flex items-center gap-2 mb-6">
                    <span className="min-w-[73px] font-medium uppercase">
                        {translate('Quantity')}
                    </span>
                    <Counter
                        value={cartCount}
                        min={1}
                        max={maximumAddableQuantity}
                        orientation="horizontal"
                        onChange={(value) => setCartCount(value)}
                    />
                </div>
                <hr className="mt-7 mb-6" />
                <div className="text-theme-secondary text-2xl font-bold flex gap-2.5 items-center">
                    {selectedVariation?.basePrice !==
                    selectedVariation?.discountedBasePrice ? (
                        <>
                            <span className="text-neutral-400 line-through text-lg">
                                {currencyFormatter(
                                    selectedVariation?.basePrice,
                                )}
                            </span>
                            <span>
                                {currencyFormatter(
                                    selectedVariation?.discountedBasePrice,
                                )}
                            </span>
                        </>
                    ) : (
                        <span>
                            {currencyFormatter(
                                selectedVariation?.basePrice ||
                                    product.basePrice,
                            )}
                        </span>
                    )}
                </div>
                <CountDown
                    label="This Deal Ends In"
                    date={Number(selectedVariation?.dealEnds) * 1000}
                    className="mt-3"
                />
                <hr className="mt-7 mb-6" />
                {product.brand ? (
                    <div className="flex items-center gap-2 mb-4">
                        <span className="min-w-[73px] uppercase font-medium">
                            {translate('Brand')}
                        </span>
                        <Link
                            to={`/brands/${product.brand.slug}`}
                            className="flex items-center gap-1.5"
                        >
                            <Image
                                src={product.brand.thumbnailImage}
                                alt={product.brand.name}
                                className="w-8 h-8 rounded-full"
                            />
                            <span>{product.brand.name}</span>
                        </Link>
                    </div>
                ) : null}

                <div className="flex items-center gap-2 mb-4">
                    <span className="min-w-[73px] uppercase font-medium">
                        {translate('Tags')}
                    </span>
                    <div className="flex flex-wrap gap-1.5 text-neutral-400">
                        {product.tags.map((item) => (
                            <Link to={`/products?tag=${item.name}`}>
                                #{item.name}
                            </Link>
                        ))}
                    </div>
                </div>
                <hr
                    className={cn('mt-7 mb-10', {
                        'max-lg:hidden': stickyButtons,
                    })}
                />
                <div
                    className={cn('flex gap-2', {
                        'max-lg:fixed bottom-[58px] left-0 right-0 max-lg:px-5 max-lg:pt-3 max-lg:pb-6 z-[2] max-lg:bg-white':
                            stickyButtons,
                    })}
                >
                    <Button
                        className="grow font-bold bg-theme-primary/10 text-theme-primary hover:bg-theme-primary hover:text-white"
                        size="lg"
                        onClick={handleAddToCart}
                        isLoading={addingToCart && !isBuyNowLoading}
                        disabled={product.stockQty < 1}
                    >
                        {translate('Add To Cart')}
                    </Button>
                    <Button
                        className="grow font-bold"
                        size="lg"
                        onClick={handleBuyNow}
                        variant="primary"
                        isLoading={isBuyNowLoading}
                        disabled={product.stockQty < 1}
                    >
                        {translate('Buy Now')}
                    </Button>
                    <IconButton
                        className={cn(
                            'h-10 w-10 md:h-12 md:w-12 text-lg md:text-xl',
                            {
                                'text-theme-alert': product.inWishlist,
                            },
                        )}
                        onClick={handleAddToWishList}
                    >
                        <FaHeart />
                    </IconButton>
                </div>
            </div>
        </div>
    );
};

export default ProductPreviewContent;
