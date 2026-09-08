const LANG_DATE_PICKER = {
            formatLocale: {
                monthsShort: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                weekdaysShort: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                weekdaysMin: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
              
            },
}
const DATE_PICKER_LOCALE_VN = {
        days: 'Chủ Nhật_Thứ Hai_Thứ Ba_Thứ Tư_Thứ Năm_Thứ Sáu_Thứ Bảy'.split('_'),
        daysShort: 'CN_Th2_Th3_Th4_Th5_Th6_Th7'.split('_'),
        months: 'Tháng 1_Tháng 2_Tháng 3_Tháng 4_Tháng 5_Tháng 6_Tháng 7_Tháng 8_Tháng 9_Tháng 10_Tháng 11_Tháng 12'.split('_'),
        monthsShort: 'Tháng 1_Tháng 2_Tháng 3_Tháng 4_Tháng 5_Tháng 6_Tháng 7_Tháng 8_Tháng 9_Tháng 10_Tháng 11_Tháng 12'.split('_'),
        firstDayOfWeek: 1
}

const sevenDaysAgoMoment = (type = 'object', format = 'YYYY/MM/DD') => {
      const from = moment().add(-7, "days").format(format)
      const to = moment().format(format)
      if(type == 'array')
      {
        return [from, to]
      }
      return {from, to};
}



export  {
    LANG_DATE_PICKER,
    DATE_PICKER_LOCALE_VN,
    sevenDaysAgoMoment,
}