import ky from "ky";

// const getToken = () => getCookie("token") as string | null;
// const getSpaceId = () => getCookie("space_id") as string | null;

const createApi = (token: string | null, spaceId: string | null) =>
  ky.create({
    prefixUrl: "/api",
    hooks: {
      beforeRequest: [
        (request) => {
          // const token = getToken();
          // const spaceId = getSpaceId();

          // Set Authorization header
          if (token) {
            request.headers.set("Authorization", `Bearer ${token}`);
            console.log("token", token);
          }

          // Add space_id query param if not present
          if (spaceId && !request.url.includes("space_id=")) {
            request.headers.set("X-Space-Id", spaceId);
            console.log("space_id", spaceId);
          }

          console.log("url", request.url);
        },
      ],
    },
  });

export { createApi };
