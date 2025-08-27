import React, { ChangeEvent, useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { BiExpand } from 'react-icons/bi';
import { FaTrashAlt } from 'react-icons/fa';
import { useSearchParams } from 'react-router-dom';
import {
    currencyFormatter,
    paddedNumber,
} from '../../frontend/utils/numberFormatter';
import Image from '../react/components/Image';
import Counter from '../react/components/inputs/Counter';
import SearchForm from '../react/components/inputs/SearchForm';
import SelectInput from '../react/components/inputs/SelectInput';
import Spinner from '../react/components/loader/Spinner';
import Pagination from '../react/components/pagination/Pagination';
import { IPaginationMeta } from '../react/types';
import { objectToFormData } from '../react/utils/ObjectFormData';
import { translate } from '../react/utils/translate';
import ProductCard from './components/ProductCard';
import OrderSubmitForm from './components/forms/OrderSubmitForm';
import AddCustomerModal from './popup/AddCustomerModal';
import HoldOrdersModal from './popup/HoldOrdersModal';
import AddressModal from './popup/address/AddressModal';
import {
    IAddress,
    ICustomer,
    IFilterData,
    IFilters,
    IPosCartGroup,
    IPosProductVariation,
} from './types';
import {
    getCustomerFilter,
    getPosFilterData,
    getPosProductsVariations,
    holdOrder,
    updatePosCartItem,
} from './utils/actions';

const App = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const [isLoading, setIsLoading] = useState({
        loadingProduct: false,
        loadingSelectedProduct: true,
    });

    const [filterData, setFilterData] = useState<IFilterData>({
        warehouses: [],
        brands: [],
        categories: [],
    });
    const [filters, setFilters] = useState<IFilters>({
        page: 1,
        warehouseId: '',
        brandId: '',
        categoryId: '',
        searchKey: '',
    });
    const [posProducts, setPosProducts] = useState<IPosProductVariation[]>([]);
    const [paginationData, setPaginationData] = useState<IPaginationMeta>();

    const [customerFilters, setCustomerFilters] = useState<ICustomer[]>();
    const [customer, setCustomer] = useState<ICustomer>();
    const [address, setAddress] = useState<IAddress | null>(null);
    const [posCartGroup, setPosCartGroup] = useState<IPosCartGroup>();

    useEffect(() => {
        getPosFilterData().then((data) => setFilterData(data));
        getCustomerFilter().then((data) => setCustomerFilters(data));
    }, []);

    useEffect(() => {
        if (!filters.warehouseId) {
            return;
        }
        setIsLoading({
            ...isLoading,
            loadingProduct: true,
        });
        const timerId = setTimeout(() => {
            getPosProductsVariations(filters)
                .then((data) => {
                    setPosProducts(data.data);
                    setPaginationData(data.meta);
                })
                .finally(() => {
                    setIsLoading({
                        ...isLoading,
                        loadingProduct: false,
                    });
                });
        }, 500);

        return () => clearTimeout(timerId);
    }, [filters]);

    useEffect(() => {
        const filters: IFilters = {
            page: Number(searchParams.get('page')) || 1,
            warehouseId: searchParams.get('warehouseId'),
            brandId: searchParams.get('brandId'),
            categoryId: searchParams.get('categoryId'),
            searchKey: searchParams.get('searchKey'),
        };
        setFilters(filters);
    }, [searchParams]);

    // Reset order when warehouse changes
    useEffect(() => {
        handleResetOrder();
    }, [searchParams.get('warehouseId')]);

    // Set posCarts and warehouseId from config if orderGroupId is in the url
    useEffect(() => {
        if (!searchParams.get('orderGroupId')) return;

        searchParams.set('warehouseId', String(window.config.posWarehouseId));
        setSearchParams(searchParams);

        // change of warehouseId in the url will trigger 'handleResetOrder'
        // which will delete all the items in the cart
        // so we need to use timeout to wait for the warehouseId to be set
        setTimeout(() => {
            setPosCartGroup(window.config.posCartGroup);
            setCustomer(window.config.posCartGroup.customer);
        }, 200);
    }, [Number(searchParams.get('orderGroupId'))]);

    const handleSearch = (e: ChangeEvent<HTMLInputElement>) => {
        searchParams.set('searchKey', e.target.value);
        setSearchParams(searchParams);
    };

    const handleFilter = (name: string, value: string | number) => {
        searchParams.set(name, String(value));
        setSearchParams(searchParams);
    };

    const handleQuantityChange = async (
        posCartId: number,
        action: 'increase' | 'decrease' | 'delete',
    ) => {
        const posCarts = await updatePosCartItem(
            objectToFormData({
                id: posCartId,
                action,
            }),
        );
        setPosCartGroup({ ...posCartGroup!, posCarts });
    };

    const handleSubmitOrderSuccess = () => {
        setPosCartGroup(undefined);
        setCustomer(undefined);
        setAddress(null);

        searchParams.delete('orderGroupId');
        setSearchParams(searchParams);
    };

    const handleHoldOrder = () => {
        if (!posCartGroup) return;

        try {
            holdOrder(posCartGroup?.posCartGroupId!);
            setPosCartGroup(undefined);
        } catch (error: any) {
            toast.error(error.response.data.message);
        }
    };

    const handleResetOrder = async () => {
        if (!posCartGroup?.posCartGroupId) return;

        await updatePosCartItem(
            objectToFormData({
                id: posCartGroup.posCartGroupId,
                action: 'delete',
            }),
        );
        setPosCartGroup(undefined);
    };

    const handleFullScreenView = () => {
        const app = document.getElementById('app');
        if (app?.requestFullscreen) {
            app.requestFullscreen();
        }
    };

    return (
        <>
            <div className="dashboard-nav pt-6 flex items-center justify-between mb-8">
                <div className="flex items-center">
                    <span className="text-xl mr-3 text-theme-secondary">
                        <i className="fa-light fa-chart-mixed"></i>
                    </span>
                    <span className="text-sm sm:text-base font-bold">
                        {translate('POS (Point Of Sales)')}
                    </span>
                </div>

                <div className="flex gap-2">
                    <HoldOrdersModal onSelectOrder={setPosCartGroup} />
                    <button
                        className="bg-theme-primary text-white h-10 w-10 text-2xl flex items-center justify-center rounded hover:bg-theme-primary/80"
                        onClick={handleFullScreenView}
                    >
                        <BiExpand />
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-5 gap-5">
                <div className="card xl:col-span-3">
                    <div className="card__content">
                        <div className="max-w-[200px] mb-3">
                            <SelectInput
                                name="warehouseId"
                                placeholder={translate('Warehouse')}
                                value={filters.warehouseId}
                                options={[
                                    {
                                        id: '',
                                        name: translate('Select Warehouse'),
                                    },
                                    ...filterData.warehouses,
                                ]}
                                getOptionLabel={(warehouse) => warehouse.name}
                                getOptionValue={(warehouse) => warehouse.id}
                                onChange={(newValue) => {
                                    handleFilter('warehouseId', newValue.id);
                                }}
                                groupClassName="grow"
                            />
                        </div>

                        <div className="flex max-sm:flex-col gap-3 sm:gap-5">
                            <SearchForm
                                placeholder={translate('Search Products...')}
                                value={filters.searchKey || ''}
                                onChange={handleSearch}
                            />

                            <div className="flex gap-5">
                                <SelectInput
                                    name="brandId"
                                    placeholder={translate('Brand')}
                                    value={filters.brandId}
                                    options={[
                                        { id: '', name: 'All brands' },
                                        ...filterData.brands,
                                    ]}
                                    getOptionLabel={(brand) => brand.name}
                                    getOptionValue={(brand) => brand.id}
                                    onChange={(newValue) => {
                                        handleFilter('brandId', newValue.id);
                                    }}
                                    groupClassName="grow"
                                />

                                <SelectInput
                                    name="categoryId"
                                    placeholder={translate('Category')}
                                    value={filters.categoryId}
                                    options={[
                                        { id: '', name: 'All categories' },
                                        ...filterData.categories,
                                    ]}
                                    getOptionLabel={(category) => category.name}
                                    getOptionValue={(category) => category.id}
                                    onChange={(newValue) => {
                                        handleFilter('categoryId', newValue.id);
                                    }}
                                    groupClassName="grow"
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-5 gap-3.5 mt-6">
                            {isLoading.loadingProduct ? (
                                <div className="col-span-full flex justify-center pt-10">
                                    <Spinner className="w-[50px] h-[50px]" />
                                </div>
                            ) : !filters.warehouseId ? (
                                <div className="col-span-full">
                                    Please select a warehouse
                                </div>
                            ) : !posProducts.length ? (
                                <div className="col-span-full">
                                    No products Found
                                </div>
                            ) : (
                                posProducts.map((posProduct) => (
                                    <ProductCard
                                        posCartGroup={posCartGroup}
                                        posProduct={posProduct}
                                        onSuccess={setPosCartGroup}
                                        key={posProduct.id}
                                    />
                                ))
                            )}
                        </div>

                        {!isLoading.loadingProduct && filters.warehouseId ? (
                            <Pagination
                                className="mt-8"
                                pagination={paginationData}
                                onPageChange={({ selected }) => {
                                    searchParams.set('page', String(selected));
                                    setSearchParams(searchParams);
                                }}
                            />
                        ) : null}
                    </div>
                </div>
                <div className="card xl:col-span-2">
                    <div className="card__content border-none pb-6">
                        <p className="text-muted text-center pb-4">
                            {translate('Total Product')}
                            <span className="text-foreground ml-3">
                                {paddedNumber(
                                    posCartGroup?.posCarts.length || 0,
                                )}
                            </span>
                        </p>

                        <div className="flex max-sm:flex-col justify-end gap-x-3 gap-y-2">
                            <SelectInput
                                name="customer"
                                placeholder={translate('Customer')}
                                value={customer?.id}
                                options={[...(customerFilters || [])]}
                                getOptionLabel={(option) =>
                                    option.name +
                                    (option.phone != null
                                        ? '(' + option.phone + ')'
                                        : '')
                                }
                                getOptionValue={(option) => option.id}
                                groupClassName="grow"
                                classNames={{
                                    control: () => 'min-w-[120px]',
                                    option: () => 'line-clamp-1',
                                    input: () =>
                                        '!grid-cols-[0_minmax(min-content,1fr)]',
                                }}
                                onChange={(newValue) => {
                                    setCustomer(newValue);
                                }}
                            />

                            <div className="flex gap-3">
                                <AddCustomerModal
                                    onSuccess={(customer) => {
                                        setCustomer(customer);
                                        setCustomerFilters([
                                            customer,
                                            ...(customerFilters || []),
                                        ]);
                                    }}
                                />
                                <AddressModal
                                    selectedAddressId={address?.id}
                                    customerId={customer?.id}
                                    onSuccess={setAddress}
                                />
                            </div>
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="theme-table">
                            <thead className="theme-table__head">
                                <tr>
                                    <th className="min-w-[180px]">
                                        {translate('Product')}
                                    </th>
                                    <th className="min-w-[80px]">
                                        {translate('Qty')}
                                    </th>
                                    <th className="min-w-[80px]">
                                        {translate('Price')}
                                    </th>
                                    <th className="min-w-[80px]">
                                        {translate('Sub Total')}
                                    </th>
                                    <th className="min-w-[50px]"></th>
                                </tr>
                            </thead>

                            <tbody className="theme-table__body">
                                {!posCartGroup?.posCarts.length ? (
                                    <tr>
                                        <td colSpan={5}>
                                            <p className="text-center">
                                                No product selected yet
                                            </p>
                                        </td>
                                    </tr>
                                ) : (
                                    posCartGroup?.posCarts.map((posCart) => (
                                        <tr
                                            className="theme-table__row"
                                            key={posCart.id}
                                        >
                                            <td>
                                                <div className="flex items-center gap-2">
                                                    
                                                <Image
                                                    className="h-[60px]"
                                                    src={posCart.product?.thumbnailImg}
                                                    alt="Product Image" 
                                                />

                                                <div>
                                                    <p className="max-w-[300px] line-clamp-1 mb-1">
                                                        {posCart.product?.name}
                                                    </p>
                                                    {posCart.variation ? (
                                                        <p>
                                                            <span className="text-muted">
                                                                {translate(
                                                                    'Variation',
                                                                )}
                                                                :
                                                            </span>{' '}
                                                            <span className="text-theme-secondary">
                                                                {
                                                                    posCart
                                                                        .variation
                                                                        .name
                                                                }
                                                            </span>
                                                        </p>
                                                    ) : null}
                                                </div>

                                                </div>
                                               
                                            </td>
                                            <td>
                                                <Counter
                                                    value={posCart.qty}
                                                    orientation="horizontal"
                                                    onChange={(newValue) => {
                                                        handleQuantityChange(
                                                            posCart.id,
                                                            newValue >
                                                                posCart.qty
                                                                ? 'increase'
                                                                : 'decrease',
                                                        );
                                                    }}
                                                />
                                            </td>
                                            <td>
                                                {currencyFormatter(
                                                    posCart.variation
                                                        ?.discountedBasePrice,
                                                )}
                                            </td>
                                            <td>
                                                {currencyFormatter(
                                                    posCart.variation
                                                        ?.discountedBasePrice *
                                                        posCart.qty,
                                                )}
                                            </td>
                                            <td>
                                                <button
                                                    className="text-theme-alert h-8 w-8 flex items-center justify-center"
                                                    onClick={() =>
                                                        handleQuantityChange(
                                                            posCart.id,
                                                            'delete',
                                                        )
                                                    }
                                                >
                                                    <FaTrashAlt />
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    <OrderSubmitForm
                        posCartGroup={posCartGroup}
                        customer={customer}
                        shippingAddress={address}
                        handleHoldOrder={handleHoldOrder}
                        handleResetOrder={handleResetOrder}
                        onSuccess={handleSubmitOrderSuccess}
                    />
                </div>
            </div>
        </>
    );
};

export default App;
