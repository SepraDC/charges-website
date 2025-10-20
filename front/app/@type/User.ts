import type { Charge } from "./Charge";

export interface User {
	id: number;
	userIdentifier: string;
	charges?: Charge[];
	roles: string[];
}
