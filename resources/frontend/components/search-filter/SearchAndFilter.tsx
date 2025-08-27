import qs from 'qs';
import { useEffect } from 'react';
import { FaHome } from 'react-icons/fa';
import { MdFilterListAlt } from 'react-icons/md';
import { useLocation, useSearchParams } from 'react-router-dom';
import { useLazyGetProductsQuery } from '../../store/features/api/productApi';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { URLFilterParams } from '../../types';
import { translate } from '../../utils/translate';
import Breadcrumb from '../Breadcrumb';
import Button from '../buttons/Button';
import ProductCard from '../card/ProductCard';
import SelectInput from '../inputs/SelectInput';
import NoDataFound from '../layouts/components/not-found/NoDataFound';
import Pagination from '../pagination/Pagination';
import ProductCardSkeleton from '../skeletons/ProductCardSkeleton';
import SectionTitle from '../titles/SectionTitle';
import FilterSidebar from './FilterSidebar';

interface Props {
    breadcrumb?: boolean;
    categorySlug?: string;
    brandSlug?: string;
    shopSlug?: string;
    discounted?: boolean;
}

const SearchAndFilter = ({
    breadcrumb = true,
    categorySlug,
    brandSlug,
    shopSlug,
    discounted = false,
}: Props) => {
    const dispatch = useAppDispatch();
    const location = useLocation();
    const [searchParams, setSearchParams] = useSearchParams();

    const [getProducts, { data: productsRes, isLoading, isFetching }] =
        useLazyGetProductsQuery();

    const products = productsRes?.products?.data;
    const brands = productsRes?.brands;
    const rootCategories = productsRes?.rootCategories;
    const filterAttributes = productsRes?.filterAttributes;
    const selectedCategory = productsRes?.selectedCategory;
    const priceFilter = productsRes?.priceFilter;

    const filters: URLFilterParams = qs.parse(location.search, {
        ignoreQueryPrefix: true,
        parseArrays: true,
        plainObjects: true,
    });
    filters.limit = 16;

    // fetch products on component mount and when the search params (url) changes
    useEffect(() => {
        filters.categorySlug = categorySlug;
        filters.brandSlug = brandSlug;
        filters.shopSlug = shopSlug;
        if (discounted) filters.discounted = discounted;

        // stringify the filters object
        const queryStr = qs.stringify(
            { ...filters, searchKey: filters.query },
            {
                addQueryPrefix: true,
                arrayFormat: 'brackets',
            },
        );
        getProducts(queryStr, true);
    }, [location.search, location.pathname]);

    // set filters to the search params (url)
    const handleFilter = (query: URLFilterParams) => {
        const str = qs.stringify(query, {
            addQueryPrefix: true,
            arrayFormat: 'brackets',
            encode: false,
        });

        setSearchParams(str);
    };

    const handleCheckboxChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { checked, value } = e.target;
        const name = e.target.name as keyof URLFilterParams;

        if (checked) {
            handleFilter({
                ...filters,
                [name]: [...((filters[name] as string[]) || []), value],
            });
        } else {
            handleFilter({
                ...filters,
                [name]: (filters[name] as string[]).filter(
                    (item) => item !== value,
                ),
            });
        }
    };

    const handlePriceChange = (values: [number, number]) => {
        handleFilter({
            ...filters,
            minPrice: values[0].toString(),
            maxPrice: values[1].toString(),
        });
    };

    const handleSortingChange = (e: {
        label: string;
        value: string | number;
    }) => {
        handleFilter({
            ...filters,
            sortBy: String(e.value),
        });
    };

    const pageTitle =
        categorySlug ||
        brandSlug ||
        shopSlug ||
        (filters.query &&
            `${translate('Search result for')} '${filters.query || ''}'`) ||
        'All Products';

    return (
        <>
            {breadcrumb && (
                <Breadcrumb
                    title={translate('Products')}
                    navigation={[
                        {
                            icon: <FaHome />,
                            name: translate('home'),
                            link: '/',
                        },
                        {
                            name: translate('Products'),
                            link: '/products',
                        },
                        {
                            name: pageTitle,
                        },
                    ]}
                />
            )}

            <div
                className={`theme-container-card no-style grid lg:grid-cols-4 mb-20 ${
                    breadcrumb && 'mt-6'
                }`}
                id="search-and-filter"
            >
                <FilterSidebar
                    isLoading={isLoading}
                    productsRes={productsRes}
                    selectedCategory={selectedCategory}
                    rootCategories={rootCategories}
                    categorySlug={categorySlug}
                    priceFilter={priceFilter}
                    brands={brands}
                    filterAttributes={filterAttributes}
                    filters={filters}
                    handleCheckboxChange={handleCheckboxChange}
                    handlePriceChange={handlePriceChange}
                    handleSortingChange={handleSortingChange}
                />

                <div className="lg:col-span-3">
                    <div className="flex items-center gap-4 border-b-2 border-theme-primary">
                        <SectionTitle
                            title={pageTitle}
                            className="grow mb-0 border-none"
                        />

                        <div className="max-lg:hidden flex items-center gap-3">
                            <span>{translate('Sorting')}</span>
                            <SelectInput
                                placeholder={translate('Sort by')}
                                classNames={{
                                    singleValue: () => '!text-neutral-400',
                                }}
                                value={filters.sortBy || 'lowToHigh'}
                                onChange={(e) =>
                                    handleFilter({
                                        ...filters,
                                        sortBy: e.value,
                                    })
                                }
                                options={[
                                    {
                                        label: translate('Price: Low to High'),
                                        value: 'lowToHigh',
                                    },
                                    {
                                        label: translate('Price: High to Low'),
                                        value: 'highToLow',
                                    },
                                    {
                                        label: translate('Newest'),
                                        value: 'newest',
                                    },
                                    {
                                        label: translate('Oldest'),
                                        value: 'oldest',
                                    },
                                ]}
                            />
                        </div>

                        <Button
                            variant="no-color"
                            className="lg:hidden border border-neutral-200 hover:bg-neutral-100"
                            onClick={() =>
                                dispatch(togglePopup('product-filter'))
                            }
                        >
                            <span className="text-base text-theme-secondary-light">
                                <MdFilterListAlt />
                            </span>
                            {translate('Filter')}
                        </Button>
                    </div>

                    <div
                        className="grid grid-cols-2 xs:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-4 mt-3 sm:mt-6 gap-2.5"
                        id="filtered-products"
                    >
                        {isFetching || !products ? (
                            Array(8)
                                .fill(0)
                                .map((_, i) => <ProductCardSkeleton key={i} />)
                        ) : products.length === 0 ? (
                            <div className="col-span-4">
                                <NoDataFound
                                    title="No Products Found"
                                    description="Seems like the product list is empty. Try browsing other categories"
                                    image="/images/no-media.png"
                                />
                            </div>
                        ) : (
                            products.map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                />
                            ))
                        )}
                    </div>

                    {productsRes?.products.meta?.last_page! > 1 && (
                        <Pagination
                            className="mt-10"
                            pagination={productsRes?.products.meta!}
                            scrollTo="#search-and-filter"
                        />
                    )}
                </div>
            </div>
        </>
    );
};

export default SearchAndFilter;
