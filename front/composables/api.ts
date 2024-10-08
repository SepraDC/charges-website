import { ofetch } from "ofetch";
import type { $Fetch, NitroFetchOptions, NitroFetchRequest } from "nitropack";
import { stringify } from "qs";
import type { ChargeType } from "@type/ChargeType";
import type { Bank } from "@type/Bank";
import type { Charge } from "@type/Charge";
import type { User } from "@type/User";

function wrapFetch(fetch: $Fetch) {
  return (
    request: NitroFetchRequest,
    options: NitroFetchOptions<never> = {}
  ): ReturnType<$Fetch> => {
    // modify request if has params in options
    let modifiedRequest, modifiedOptions;
    if (options?.query) {
      // append query string and delete query from options
      modifiedRequest = request + "?" + stringify(options.query);
      modifiedOptions = options;
      delete modifiedOptions.query;
    } else {
      // use as is
      modifiedRequest = request;
      modifiedOptions = options;
    }

    return fetch(modifiedRequest, modifiedOptions);
  };
}

export const useApi = () => {
  const config = useRuntimeConfig();

  const { token } = useAuthState();

  return wrapFetch(
    ofetch.create({
      baseURL: `${config.API_BASE_URL || config.public.API_BASE_URL}`,
      headers: {
        "Content-Type": "application/json",
        Accept: "application/ld+json",
        authorization: `Bearer ${token.value}`,
      },
    })
  );
};

export interface CollectionResponse<T> {
  "@context": string;
  "@id": string;
  "@type": string;
  "hydra:member": T[];
  "hydra:itemsPerPage": number;
  "hydra:totalItems": number;
}

export class Pagination {
  public page = 1;
  public totalItems = 0;
  public itemsPerPage = 30;

  static fromCollectionResponse(response: CollectionResponse<unknown>) {
    const pagination = new Pagination();

    pagination.totalItems = response["hydra:totalItems"];
    pagination.itemsPerPage = response["hydra:itemsPerPage"];
    return pagination;
  }
}

export interface CollectionFacade<T> {
  data: CollectionResponse<T>;
  members: T[];
  totalItems: number;
  pagination: Pagination;
}

export function exposeCollectionAsync<T>(
  response: Promise<CollectionResponse<T>>
): Promise<CollectionFacade<T>> {
  return response.then(
    <T>(response: CollectionResponse<T>): CollectionFacade<T> => ({
      data: response,
      members: response["hydra:member"],
      totalItems: response["hydra:totalItems"],
      pagination: Pagination.fromCollectionResponse(response),
    })
  );
}

const operations = <T>(api: $Fetch<T>, prefix: NitroFetchRequest = "") => ({
  get:
    <U>(url: NitroFetchRequest = "") =>
    (id: string, options: NitroFetchOptions<never> = {}) =>
      api<U>(`${prefix}${url}/${id}`, options),
  getCollection:
    <T>(url: NitroFetchRequest = "") =>
    (options: NitroFetchOptions<never> = {}) =>
      exposeCollectionAsync(
        api<CollectionResponse<T>>(`${prefix}${url}`, options)
      ),
  post:
    (url: NitroFetchRequest = "") =>
    (options: NitroFetchOptions<never> = {}) =>
      api(`${prefix}${url}`, { method: "POST", ...options }),
  put:
    (url: NitroFetchRequest = "") =>
    (id: string, options: NitroFetchOptions<never> = {}) =>
      api(`${prefix}${url}/${id}`, { method: "PUT", ...options }),
  patch:
    (url: NitroFetchRequest = "") =>
    (id: string | null, options: NitroFetchOptions<never> = {}) =>
      api(`${prefix}${url}/${id}`, { method: "PATCH", ...options }),
  delete:
    (url: NitroFetchRequest = "") =>
    (id: string, options: NitroFetchOptions<never> = {}) =>
      api(`${prefix}${url}/${id}`, { method: "DELETE", ...options }),
});

export const useApiRoutes = () => {
  const api = useApi();

  const prefixOperations = <T>(
    prefix: string,
    getter: (op: ReturnType<typeof operations>) => T
  ): T => {
    return getter(operations(api, prefix));
  };

  return {
    auth: prefixOperations("auth", ({ post }) => ({
      login: (values: { username: string; password: string }) =>
        post()({ body: values }),
    })),
    verify: prefixOperations("verify", ({ get }) => ({
      verify: get<User>(),
    })),
    banks: prefixOperations("banks", ({ get, getCollection }) => ({
      get: get<Bank>(),
      getCollection: getCollection<Bank>(),
      getUserBanks: getCollection<Bank>("/user"),
    })),
    charges: prefixOperations(
      "charges",
      ({ get, getCollection, delete: remove, post, put }) => ({
        get: get<Charge>(),
        getCollection: (query = {}) => getCollection()({ params: query }),
        create: (values: never) => post()({ body: values }),
        update: (id: string, values: never) => put()(id, { body: values }),
        delete: (id: string) => remove()(id),
        reset: () => api("/charges/reset", { method: "PATCH", body: {} }),
      })
    ),
    chargeTypes: prefixOperations("charge_types", ({ get, getCollection }) => ({
      get: get<ChargeType>(),
      getCollection: getCollection(),
    })),
  };
};

export const getMembers = <U>(
  promise: Promise<CollectionFacade<U>>
): Promise<U[]> => {
  return promise.then((i) => i.members);
};
