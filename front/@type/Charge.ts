import type { Bank } from "@type/Bank";
import type { ChargeType } from "@type/ChargeType";
import type { User } from "@type/User";

export interface Charge {
	"@id"?: string;
	id?: number;
	name: string;
	amount: number;
	state: boolean;
	bank?: Bank;
	chargeType?: ChargeType;
	user?: User;
	date?: string;
}

export type ChargeResponse = Charge;
