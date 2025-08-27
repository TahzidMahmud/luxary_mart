import { ChangeEvent } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import {
    IFilterAttribute,
    ISearchAndFilter,
} from '../../store/features/api/productApi';
import { closePopup, usePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { ICategoryShort, URLFilterParams } from '../../types';
import { IBrandShort } from '../../types/brand';
import { translate } from '../../utils/translate';
import PriceSlider from '../inputs/PriceSlider';
import SelectInput from '../inputs/SelectInput';
import SectionTitle from '../titles/SectionTitle';
import AttributeFilterCard from './filter-cards/AttributeFilterCard';
import BrandFilterCard from './filter-cards/BrandFilterCard';
import FilterCategoryCard from './filter-cards/FilterCategoryCard';

interface Props {
    isLoading: boolean;
    productsRes?: ISearchAndFilter;
    selectedCategory?: ICategoryShort | null;
    rootCategories?: ICategoryShort[] | undefined;
    categorySlug: string | undefined;
    priceFilter?: ISearchAndFilter['priceFilter'];
    brands: IBrandShort[] | undefined;
    filterAttributes: IFilterAttribute[] | undefined;
    filters: URLFilterParams;
    handleCheckboxChange: (e: ChangeEvent<HTMLInputElement>) => void;
    handlePriceChange: (values: [number, number]) => void;
    handleSortingChange: (option: any) => void;
}

const FilterSidebar = ({
    isLoading,
    productsRes,
    selectedCategory,
    rootCategories,
    categorySlug,
    priceFilter,
    brands,
    filterAttributes,
    filters,
    handleCheckboxChange,
    handlePriceChange,
    handleSortingChange,
}: Props) => {
    const dispatch = useAppDispatch();
    const { popup } = usePopup();

    return (
        <aside
            className={`product-filter-sidebar ${
                popup === 'product-filter' && 'translate-x-0 delay-150'
            }`}
        >
            <div className="flex items-center justify-between mb-5 gap-4">
                <SectionTitle title="Filters" className="grow mb-0" />
                <button
                    className="text-lg lg:hidden"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            {isLoading || !productsRes ? (
                <div className="space-y-2.5">
                    {Array(3)
                        .fill(0)
                        .map((_, i) => (
                            <div
                                className="border border-theme-primary-14 py-3 md:py-5 px-4 md:px-8 rounded-md"
                                key={i}
                            >
                                <div className="skeleton h-6 pb-2 w-1/2 mb-8"></div>

                                <div className="mt-5 flex flex-col gap-3">
                                    <div className="skeleton h-4 w-4/6"></div>
                                    <div className="skeleton h-4 w-5/6"></div>
                                    <div className="skeleton h-4 w-2/6"></div>
                                    <div className="skeleton h-4 w-3/6"></div>
                                    <div className="skeleton h-4 w-5/6"></div>
                                    <div className="skeleton h-4 w-2/6"></div>
                                </div>
                            </div>
                        ))}
                </div>
            ) : (
                <>
                    <div className="lg:hidden flex items-center gap-3 mb-4">
                        <span>{translate('Sorting')}</span>
                        <div className="grow">
                            <SelectInput
                                placeholder={translate('Sort by')}
                                classNames={{
                                    singleValue: () => '!text-neutral-400',
                                }}
                                value={filters.sortBy || 'lowToHigh'}
                                onChange={handleSortingChange}
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
                    </div>

                    <FilterCategoryCard
                        selectedCategory={selectedCategory}
                        rootCategories={rootCategories}
                        categorySlug={categorySlug}
                    />

                    <div className="border border-theme-primary-14 bg-white py-3 md:pt-5 md:pb-7 px-4 md:px-8 rounded-md mt-2.5">
                        <h5 className="text-sm font-public-sans pb-2 border-b border-theme-primary-14 text-zinc-900 uppercase">
                            {translate('Price')}
                        </h5>

                        <div className="mt-5 space-y-3">
                            {priceFilter && (
                                <PriceSlider
                                    options={{
                                        range: {
                                            min:
                                                Number(priceFilter.minPrice) ||
                                                0,
                                            max:
                                                Number(priceFilter.maxPrice) ||
                                                100,
                                        },
                                        start: [
                                            Number(filters.minPrice) ||
                                                Number(priceFilter.minPrice) ||
                                                0,
                                            Number(filters.maxPrice) ||
                                                Number(priceFilter.maxPrice) ||
                                                100,
                                        ],
                                    }}
                                    onChange={handlePriceChange}
                                />
                            )}
                        </div>
                    </div>

                    <BrandFilterCard
                        brands={brands!}
                        filters={filters}
                        onChange={handleCheckboxChange}
                    />

                    {filterAttributes?.map((attribute) => (
                        <AttributeFilterCard
                            attribute={attribute!}
                            filters={filters}
                            handleCheckboxChange={handleCheckboxChange}
                            key={attribute?.id}
                        />
                    ))}
                </>
            )}
        </aside>
    );
};

export default FilterSidebar;
