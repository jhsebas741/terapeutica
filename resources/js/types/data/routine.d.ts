import type { Pictogram } from './pictogram';

export interface RoutineStep {
    id?: number;
    pictogram_id: number;
    order: number;
    pictogram?: Pictogram;
}

export interface Routine {
    id: number;
    patient_id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    patient?: { id: number; name: string };
    routine_steps?: RoutineStep[];
    routine_steps_count?: number;
}
