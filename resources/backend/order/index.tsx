import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { BrowserRouter } from 'react-router-dom';
import AppContextProvider from '../react/Context';
import PopupIndex from '../react/components/popup/PopupIndex';
import '../react/i18next';
import Orders from '../react/pages/Orders';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Provider } from 'react-redux';
import { store } from '../store';
import Popups from '../../frontend/components/popups/index'
import ConfirmStatusUpdatePopup from '../../frontend/components/popups/order/ConfirmStatusUpdatePopup';
// collapse sidebar
const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}
const queryClient = new QueryClient();

const root = createRoot(document.getElementById('app')!);
root.render(
<Provider store={store}>
    <QueryClientProvider client={queryClient}>
        <BrowserRouter>
            <AppContextProvider>
                <Toaster />
                <Orders />
                <PopupIndex />
                  <ConfirmStatusUpdatePopup/>
            </AppContextProvider>
        </BrowserRouter>
    </QueryClientProvider>
</Provider>,
);
