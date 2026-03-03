import type { PatientSetting } from './patient-settings';

export interface Patient {
    id: number;
    name: string;
    email: string;
    birth_date: string;
    diagnosis: string;
    avatar_url: string;
    is_active: boolean;
    patient_setting?: PatientSetting;
}
