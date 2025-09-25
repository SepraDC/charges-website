import type { Charge } from "../@type/Charge";

export interface GroupedCharge {
	dayOfWithdrawal: number;
	charges: Charge[];
}

export function useChargeGrouping() {
	const groupChargesByDay = (charges: Charge[]): GroupedCharge[] => {
		if (!charges || charges.length === 0) return [];

		// Group charges by dayOfWithdrawal
		const grouped = charges.reduce(
			(acc, charge) => {
				const day = charge.dayOfWithdrawal || 0;

				if (!acc[day]) {
					acc[day] = [];
				}
				acc[day].push(charge);

				return acc;
			},
			{} as Record<number, Charge[]>,
		);

		// Convert to array and sort by day
		return Object.entries(grouped)
			.map(([day, charges]) => ({
				dayOfWithdrawal: parseInt(day),
				charges: charges.sort((a, b) => a.name.localeCompare(b.name)),
			}))
			.sort((a, b) => a.dayOfWithdrawal - b.dayOfWithdrawal);
	};

	const formatDayLabel = (day: number): string => {
		return `Prélèvement prévu le ${day} ${new Date().toLocaleDateString("fr-FR", { month: "long" })}`;
	};

	return {
		groupChargesByDay,
		formatDayLabel,
	};
}
