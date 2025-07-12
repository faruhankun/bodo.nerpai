import React from "react";
import ReactDOM from "react-dom/client";
import SpacesPage from "./app/(dashboard)/space/spaces/page";

const el = document.getElementById("react-page-root") as HTMLElement;

const token = el.dataset.token ?? null;
const spaceId = el.dataset.spaceId ?? null;

if (el) {
  ReactDOM.createRoot(el).render(
    <React.StrictMode>
      <SpacesPage token={token} spaceId={spaceId} />
    </React.StrictMode>
  );
}
