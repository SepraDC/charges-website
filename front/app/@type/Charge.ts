import type { Bank } from "./Bank";
import type { ChargeType } from "./ChargeType";
import type { User } from "./User";

export interface Charge {
	"@id"?: string;
	id?: number;
	name: string;
	amount: number | string;
	state: boolean;
	bank?: Bank;
	chargeType?: ChargeType;
	user?: User | string;
	dayOfWithdrawal?: number;
}

export type ChargeResponse = Charge;
