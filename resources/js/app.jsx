import React from 'react';
import ReactDOM from 'react-dom/client';
import AccountModalApp from './AccountModalApp';

const rootElement = document.getElementById('react-account-modal');
if (rootElement) {
    ReactDOM.createRoot(rootElement).render(<AccountModalApp />);
}
