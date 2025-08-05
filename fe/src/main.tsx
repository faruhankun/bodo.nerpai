import React from "react";
import ReactDOM from "react-dom/client";
import SpacesPage from "./app/(dashboard)/space/spaces/page";
import AccountsPage from "./app/(dashboard)/inventory/accounts/page";

import SuppliesSummariesPage from "./app/(dashboard)/inventory/supplies/summary/page";

import { setApiContext } from "./api";

const elAccounts = document.getElementById("react-accounts-page");
const elSpaces = document.getElementById("react-page-spaces");
const elSuppliesSummary = document.getElementById("react-supplies-summary-page");

if (elSpaces) {
  const token = elSpaces.dataset.token ?? null;
  const spaceId = elSpaces.dataset.spaceId ?? null;
  setApiContext(token, spaceId);
  console.log("elSpaces", token, spaceId);

  ReactDOM.createRoot(elSpaces).render(
    <React.StrictMode>
      <SpacesPage token={token} spaceId={spaceId} />
    </React.StrictMode>
  );
}

if (elAccounts) {
  const token = elAccounts.dataset.token ?? null;
  const spaceId = elAccounts.dataset.spaceId ?? null;
  setApiContext(token, spaceId);
  console.log("elAccounts", token, spaceId);

  ReactDOM.createRoot(elAccounts).render(
    <React.StrictMode>
      <AccountsPage />
    </React.StrictMode>
  );
}

if(elSuppliesSummary) {
  const token = elSuppliesSummary.dataset.token ?? null;
  const spaceId = elSuppliesSummary.dataset.spaceId ?? null;
  setApiContext(token, spaceId);
  console.log("elSuppliesSummary", token, spaceId);

  ReactDOM.createRoot(elSuppliesSummary).render(
    <React.StrictMode>
      <SuppliesSummariesPage />
    </React.StrictMode>
  );
}
