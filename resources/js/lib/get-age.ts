export function getAge(birthDate: string | Date): number {
    const birthDateParsed =
        birthDate instanceof Date ? birthDate : new Date(birthDate);
    const today = new Date();
    let age = today.getFullYear() - birthDateParsed.getFullYear();
    const month = today.getMonth() - birthDateParsed.getMonth();
    if (
        month < 0 ||
        (month === 0 && today.getDate() < birthDateParsed.getDate())
    ) {
        age--;
    }
    return age;
}
