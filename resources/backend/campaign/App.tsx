import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { debounce } from '../../frontend/utils/debounce';
import SelectInput from '../react/components/inputs/SelectInput';
import Pagination from '../react/components/pagination/Pagination';
import { IPaginatedResponse } from '../react/types';
import { translate } from '../react/utils/translate';
import CampaignProduct from './components/CampaignProduct';
import ProductSelect from './components/ProductSelect';
import SearchForm from './components/SearchForm';
import {
    IBrands,
    ICampaignProduct,
    ICategory,
    IProductFilter,
    IProductShort,
} from './types';
import {
    addProductToCampaign,
    getCampaignProducts,
    getFilterData,
    getProducts,
} from './utils';

const initialCampaignData: IPaginatedResponse<any> = {
    data: [],
    meta: {
        current_page: 1,
        from: 1,
        last_page: 1,
        per_page: 20,
        to: 1,
        total: 1,
    },
};
const debouncedSearch = debounce((fn: Function) => fn(), 500);

declare global {
    interface Window {
        urlType: string;
    }
}

const App = () => {
    const campaignMatches = window.location.pathname.match(
        /\/(admin|seller)\/campaigns\/(\d+)/,
    );
    const campaignId = campaignMatches ? campaignMatches[2] : null;

    const [loading, setLoading] = useState({
        filteredProducts: false,
        selectedProducts: false,
        filterData: true,
    });

    // category and brand data for filtering
    const [filterData, setFilterData] = useState<{
        brands: IBrands[];
        categories: ICategory[];
    }>({
        brands: [],
        categories: [],
    });

    const [filters, setFilters] = useState<IProductFilter>({
        page: 1,
        limit: 20,

        searchKey: '',

        brandId: null,
        categoryId: null,
    });

    const [campaignProductFilters, setCampaignProductFilters] =
        useState<IProductFilter>({
            page: 1,
            limit: 15,

            searchKey: '',

            brandId: null,
            categoryId: null,
        });

    // selected products and pagination data
    const [campaignProducts, setCampaignProducts] =
        useState<IPaginatedResponse<ICampaignProduct[]>>(initialCampaignData);

    // filtered products and pagination data
    const [filteredProducts, setFilteredProducts] =
        useState<IPaginatedResponse<IProductShort[]>>(initialCampaignData);

    // selected products to be added to campaign
    // example: { productId: variationId[] }
    const [selectedProducts, setSelectedProducts] = useState<
        Record<string, number[]>
    >({});

    // update filter when url changes
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);

        const page = Number(urlParams.get('page')) || 1;
        const limit = Number(urlParams.get('limit')) || 20;

        const searchKey = urlParams.get('searchKey') || '';

        const brandId = Number(urlParams.get('brandId')) || null;
        const categoryId = Number(urlParams.get('categoryId')) || null;

        setFilters({
            ...filters,
            page,
            limit,
            searchKey,
            brandId,
            categoryId,
        });
    }, [window.location.search]);

    useEffect(() => {
        getFilterData()
            .then((filterData) => {
                setFilterData(filterData);
            })
            .catch((error) => {
                toast.error(error.response.data.message);
            })
            .finally(() => {
                setLoading((prev) => ({ ...prev, filterData: false }));
            });
    }, []);

    // fetch products when filters change
    useEffect(() => {
        setLoading((prev) => ({ ...prev, filteredProducts: true }));

        // wrap getProducts in debouncedSearch to prevent
        // multiple api calls when user types
        // debouncedSearch(() => {
        getProducts(filters)
            .then((data) => {
                setFilteredProducts(data);
            })
            .finally(() => {
                setLoading((prev) => ({
                    ...prev,
                    filteredProducts: false,
                }));
            });
        // });
    }, [filters]);

    // fetch products when campaignProductFilters change
    useEffect(() => {
        setLoading((prev) => ({ ...prev, selectedProducts: true }));

        // wrap getCampaignProducts in debouncedSearch to prevent
        // multiple api calls when user types
        debouncedSearch(() =>
            getCampaignProducts({
                ...campaignProductFilters,
                campaignId: Number(campaignId),
            })
                .then((data) => {
                    setCampaignProducts(data);
                })
                .finally(() => {
                    setLoading((prev) => ({
                        ...prev,
                        selectedProducts: false,
                    }));
                }),
        );
    }, [campaignProductFilters]);

    const handleSelectVariation = ({
        productId,
        variationIds,
    }: {
        productId: number;
        variationIds: number[];
    }) => {
        setSelectedProducts((prev) => {
            return {
                ...prev,
                [productId]: variationIds,
            };
        });
    };

    const handleAddToCampaign = async () => {
        try {
            const variationIds = Object.values(selectedProducts).flat();
            if (!variationIds.length) {
                toast.error(translate('Select at least one product'));
                return;
            }

            const newCampaignProducts = await addProductToCampaign(
                Number(campaignId),
                variationIds,
            );

            if (newCampaignProducts?.length) {
                setCampaignProducts((prev) => {
                    return {
                        ...prev,
                        data: [...newCampaignProducts, ...prev!.data],
                    };
                });
            }

            setSelectedProducts({});
            toast.success(translate('Products added to campaign'));
        } catch (error) {
            toast.error(
                error.response.data.message ||
                    translate('Something went wrong!'),
            );
        }
    };

    const handleRemoveFromCampaign = async (id: number) => {
        const updatedProducts = campaignProducts.data.filter(
            (p) => p.id !== id,
        );
        setCampaignProducts({
            ...campaignProducts,
            data: updatedProducts,
        });
    };

    return (
        <div className="card grid grid-cols-12 mt-10 !px-0">
            <div className="col-span-12 xl:col-span-5 pb-4 flex flex-col min-h-[700px] lg:max-h-[calc(100vh-150px)]">
                <h4 className="card__title">
                    <span className="px-3">{translate('Select Products')}</span>
                </h4>

                <div className="card__content flex gap-2.5 lg:!py-3 lg:!px-10">
                    <SelectInput
                        name="brandId"
                        placeholder={translate('Brand')}
                        value={filters.brandId}
                        options={[
                            { id: null, name: 'All brands' },
                            ...filterData.brands,
                        ]}
                        getOptionLabel={(brand) => brand.name}
                        getOptionValue={(brand) => brand.id}
                        onChange={(newValue) => {
                            setFilters({
                                ...filters,
                                brandId: newValue.id,
                            });
                        }}
                    />

                    <SelectInput
                        name="categoryId"
                        placeholder={translate('Category')}
                        value={filters.categoryId}
                        options={[
                            { id: null, name: 'All categories' },
                            ...filterData.categories,
                        ]}
                        getOptionLabel={(category) => category.name}
                        getOptionValue={(category) => category.id}
                        onChange={(newValue) => {
                            setFilters({
                                ...filters,
                                categoryId: newValue.id,
                            });
                        }}
                    />

                    <SearchForm
                        placeholder={translate('Search Products...')}
                        value={filters.searchKey}
                        onChange={(e) =>
                            setFilters((prev) => ({
                                ...prev,
                                searchKey: e.target.value,
                            }))
                        }
                    />
                </div>

                <div className="uppercase text-left bg-theme-primary/5 flex items-center justify-between text-muted px-2 md:px-10 py-3">
                    <div className="font-bold">{translate('Products')}</div>

                    <div className="flex items-center gap-3">
                        <p className="md:hidden xl:block">
                            {translate('Selected')}:
                        </p>
                        <p className="md:hidden xl:block">
                            {Object.values(selectedProducts).flat().length}
                        </p>

                        <button
                            className="button button--primary"
                            onClick={handleAddToCampaign}
                        >
                            <span>
                                <i className="fal fa-plus"></i>
                            </span>
                            {translate('Add Product')}
                        </button>
                    </div>
                </div>

                <div className="grow overflow-y-auto divide-y divide-border">
                    {loading.filteredProducts ? (
                        <p className="py-5 text-center">
                            {translate('Loading products...')}
                        </p>
                    ) : !filteredProducts?.data.length ? (
                        <p className="py-5 text-center">
                            {translate('No products found')}
                        </p>
                    ) : (
                        filteredProducts?.data.map((product) => (
                            <ProductSelect
                                product={product}
                                selectedVariations={
                                    selectedProducts[product.id] || []
                                }
                                onVariationSelect={handleSelectVariation}
                                key={product.id}
                            />
                        ))
                    )}
                </div>

                <Pagination
                    pagination={filteredProducts?.meta}
                    className="px-10"
                    onPageChange={({ selected }) =>
                        setFilters({ ...filters, page: selected })
                    }
                />
            </div>

            <div className="col-span-12 xl:col-span-7 pb-4 max-2xl:border-t 2xl:border-l border-border flex flex-col min-h-[700px] lg:max-h-[calc(100vh-150px)]">
                <h4 className="card__title">
                    <span className="lg:px-3">
                        {translate('Added Products')}{' '}
                        <span className="text-muted font-normal">
                            ({campaignProducts?.data.length || '0'})
                        </span>
                    </span>
                </h4>

                <div className="card__content !pb-3 pt-3 flex justify-between ">
                    <div className="flex gap-2.5 lg:px-3">
                        <SelectInput
                            name="brandId"
                            placeholder={translate('Brand')}
                            value={campaignProductFilters.brandId}
                            options={[
                                { id: null, name: 'All brands' },
                                ...filterData.brands,
                            ]}
                            getOptionLabel={(brand) => brand.name}
                            getOptionValue={(brand) => brand.id}
                            onChange={(newValue) => {
                                setCampaignProductFilters({
                                    ...campaignProductFilters,
                                    brandId: newValue.id,
                                });
                            }}
                        />

                        <SelectInput
                            name="categoryId"
                            placeholder={translate('Category')}
                            value={campaignProductFilters.categoryId}
                            options={[
                                { id: null, name: 'All categories' },
                                ...filterData.categories,
                            ]}
                            getOptionLabel={(category) => category.name}
                            getOptionValue={(category) => category.id}
                            onChange={(newValue) => {
                                setCampaignProductFilters({
                                    ...campaignProductFilters,
                                    categoryId: newValue.id,
                                });
                            }}
                        />
                    </div>

                    <div className="max-w-[250px] w-full">
                        <SearchForm
                            placeholder={translate('Search Products...')}
                            value={campaignProductFilters.searchKey}
                            onChange={(e) =>
                                setCampaignProductFilters((prev) => ({
                                    ...prev,
                                    searchKey: e.target.value,
                                }))
                            }
                        />
                    </div>
                </div>

                <div className="overflow-y-auto grow">
                    <div>
                        <table className="w-full min-w-[600px]">
                            {/* footable */}
                            <thead className="theme-table__head text-muted">
                                <tr>
                                    <th className="">
                                        {translate('Product Details')}
                                    </th>
                                    <th>{translate('Price')} </th>
                                    <th>{translate('Discount')}</th>
                                    <th>{translate('Discount Type')}</th>
                                    <th className="w-[60px]"></th>
                                </tr>
                            </thead>
                            <tbody className="[&_td]:py-3 divide-y divide-border">
                                {loading.selectedProducts ? (
                                    <tr>
                                        <td className="" colSpan={5}>
                                            <p className="text-center py-10">
                                                {translate('Loading...')}
                                            </p>
                                        </td>
                                    </tr>
                                ) : !campaignProducts?.data.length ? (
                                    <tr>
                                        <td className="" colSpan={5}>
                                            <p className="text-center py-10">
                                                {translate('No Product Found')}
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    campaignProducts?.data.map((item) => (
                                        <CampaignProduct
                                            campaignProduct={item}
                                            onDelete={() =>
                                                handleRemoveFromCampaign(
                                                    item.id,
                                                )
                                            }
                                            key={item.id}
                                        />
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <Pagination
                    pagination={campaignProducts?.meta}
                    className="px-10"
                    onPageChange={({ selected }) =>
                        setCampaignProductFilters({
                            ...campaignProductFilters,
                            page: selected,
                        })
                    }
                />
            </div>
        </div>
    );
};

export default App;
