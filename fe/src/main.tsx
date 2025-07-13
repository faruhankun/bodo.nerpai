import React from "react";
import ReactDOM from "react-dom/client";
import SpacesPage from "./app/(dashboard)/space/spaces/page";

const el = document.getElementById("react-page-root") as HTMLElement;

const token = el.dataset.token ?? null;
const spaceId = el.dataset.spaceId ?? null;

const el_spaces = document.getElementById("react-page-spaces") as HTMLElement;
const el_accounts = document.getElementById("react-page-accounts") as HTMLElement;


if (el) {
  if(el_spaces){
    ReactDOM.createRoot(el_spaces).render(
      <React.StrictMode>
        <SpacesPage token={token} spaceId={spaceId} />
      </React.StrictMode>
    );
  }

  if(el_accounts){
    ReactDOM.createRoot(el_accounts).render(
      <React.StrictMode>
        <SpacesPage token={token} spaceId={spaceId} />
      </React.StrictMode>
    );
  }
}
