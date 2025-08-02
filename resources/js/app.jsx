import ReactDOM from 'react-dom/client';
import AccountModalApp from './AccountModalApp';
import SuppliesModalApp from './SuppliesModalApp';

const rootElement = document.getElementById('react-account-modal');
if (rootElement) {
    ReactDOM.createRoot(rootElement).render(<AccountModalApp />);
}


const suppliesModalElement = document.getElementById('react-supplies-modal');
if (suppliesModalElement) {
    ReactDOM.createRoot(suppliesModalElement).render(<SuppliesModalApp />);
}
