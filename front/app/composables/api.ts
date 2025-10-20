import type { $Fetch, NitroFetchOptions, NitroFetchRequest } from "nitropack";
import { ofetch } from "ofetch";
import { stringify } from "qs";
import type { Bank } from "../@type/Bank";
import type { Charge } from "../@type/Charge";
import type { ChargeType } from "../@type/ChargeType";
import type { User } from "../@type/User";
import { useAuth } from "../composables/auth";

function wrapFetch(fetch: $Fetch) {
	return (
		request: NitroFetchRequest,
		options: NitroFetchOptions<never> = {},
	): ReturnType<$Fetch> => {
		// modify request if has params in options
		let modifiedRequest: NitroFetchRequest;
		let modifiedOptions: NitroFetchOptions;
		if (options?.query) {
			// append query string and delete query from options
			modifiedRequest = `${request}?${stringify(options.query)}`;
			modifiedOptions = options;
			modifiedOptions.query = undefined;
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

	const { token } = useAuth();

	let baseApiUrl = import.meta.client
		? config.public.apiBaseURL
		: config.apiBaseURL;
	return wrapFetch(
		ofetch.create({
			baseURL: baseApiUrl,
			headers: {
				"Content-Type": "application/json",
				Accept: "application/ld+json",
				authorization: `Bearer ${token.value}`,
			},
		}),
	);
};

export interface CollectionResponse<T> {
	"@context": string;
	"@id": string;
	"@type": string;
	member: T[];
	"hydra:itemsPerPage": number;
	totalItems: number;
}

export class Pagination {
	public page = 1;
	public totalItems = 0;
	public itemsPerPage = 30;

	static fromCollectionResponse(response: CollectionResponse<unknown>) {
		const pagination = new Pagination();

		pagination.totalItems = response.totalItems;
		pagination.itemsPerPage = response["hydra:itemsPerPage"];
		return pagination;
	}
}

export interface CollectionFacade<T> {
	data: CollectionResponse<T>;
	member: T[];
	totalItems: number;
	pagination: Pagination;
}

export async function exposeCollectionAsync<T>(
	response: Promise<CollectionResponse<T>>,
): Promise<CollectionFacade<T>> {
	let response1 = await response;
	return {
		data: response1,
		member: response1.member,
		totalItems: response1.totalItems,
		pagination: Pagination.fromCollectionResponse(response1),
	};
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
				api<CollectionResponse<T>>(`${prefix}${url}`, options),
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
			api(`${prefix}${url}/${id}`, {
				method: "PATCH",
				headers: { "Content-Type": "application/merge-patch+json" },
				...options,
			}),
	delete:
		(url: NitroFetchRequest = "") =>
		(id: string, options: NitroFetchOptions<never> = {}) =>
			api(`${prefix}${url}/${id}`, { method: "DELETE", ...options }),
});

export const useApiRoutes = () => {
	const api = useApi();

	const prefixOperations = <T>(
		prefix: string,
		getter: (op: ReturnType<typeof operations>) => T,
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
			({ get, getCollection, delete: remove, post, patch }) => ({
				get: get<Charge>(),
				getCollection: (query = {}) =>
					getCollection<Charge>()({ params: query }),
				create: (values: Partial<Charge>) => post()({ body: values }),
				update: (id: string, values: Partial<Charge>) =>
					patch()(id, {
						body: values,
					}),
				delete: (id: string) => remove()(id),
				reset: () =>
					api("/charges/reset", {
						method: "PATCH",
						headers: { "Content-Type": "application/merge-patch+json" },
						body: {},
					}),
			}),
		),
		chargeTypes: prefixOperations("charge_types", ({ get, getCollection }) => ({
			get: get<ChargeType>(),
			getCollection: getCollection<ChargeType>(),
		})),
	};
};

export const getMembers = <U>(
	promise: Promise<CollectionFacade<U>>,
): Promise<U[]> => {
	return promise.then((i) => i.member);
};
