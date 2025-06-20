import { useState, useEffect } from 'react';
import AccountTransactionModal from './components/primary/transaction/journal_account/AccountTransactionModal';


const AccountModalApp = () => {
  const [open, setOpen] = useState(false);
  const [accountId, setAccountId] = useState(null);
  const [startDate, setStartDate] = useState(null);
  const [endDate, setEndDate] = useState(null);
  const [accountData, setAccountData] = useState(null);

  useEffect(() => {
    const handler = () => {
      const container = document.getElementById('react-account-modal');
      const id = container?.getAttribute('data-id');
      const start_date = container?.getAttribute('data-start_date');
      const end_date = container?.getAttribute('data-end_date');
      const accountData = container?.getAttribute('data-account_data');
      setAccountId(id);
      setStartDate(start_date);
      setEndDate(end_date);
      setAccountData(JSON.parse(accountData) || accountData);
      setOpen(true);
    };

    window.addEventListener('showAccountModal', handler);
    return () => window.removeEventListener('showAccountModal', handler);
  }, []);

  return (
    <AccountTransactionModal
      visible={open}
      onClose={() => setOpen(false)}
      accountId={accountId}
      startDate={startDate}
      endDate={endDate}
      accountData={accountData}
    />
  );
};

export default AccountModalApp;