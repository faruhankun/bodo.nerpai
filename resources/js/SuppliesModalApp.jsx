import { useState, useEffect } from 'react';
import SuppliesTransactionModal from './components/primary/inventory/supplies/SuppliesTransactionModal';



const SuppliesModalApp = () => {
  const [open, setOpen] = useState(false);
  const [accountId, setSuppliesId] = useState(null);
  const [startDate, setStartDate] = useState(null);
  const [endDate, setEndDate] = useState(null);
  const [accountData, setSuppliesData] = useState(null);

  useEffect(() => {
    const handler = () => {
      const container = document.getElementById('react-supplies-modal');
      const id = container?.getAttribute('data-id');
      const start_date = container?.getAttribute('data-start_date');
      const end_date = container?.getAttribute('data-end_date');
      const accountData = container?.getAttribute('data-account_data');
      setSuppliesId(id);
      setStartDate(start_date);
      setEndDate(end_date);
      setSuppliesData(JSON.parse(accountData) || accountData);
      setOpen(true);
    };

    window.addEventListener('showSuppliesModal', handler);
    return () => window.removeEventListener('showSuppliesModal', handler);
  }, []);

  return (
    <SuppliesTransactionModal
      visible={open}
      onClose={() => setOpen(false)}
      accountId={accountId}
      startDate={startDate}
      endDate={endDate}
      accountData={accountData}
    />
  );
};

export default SuppliesModalApp;