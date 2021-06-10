export default {
	questionTypeList : state => {
		return state.questionTypeList
	},
	questionTypeById: (state) => (id) => {
		return state.questionTypeList.find(questionType => questionType.id === id)
	},
	
	getCurrentQuestionType : state => {
		return state.currentQuestionType
	},

	getCurrentQuestionTypeNameByLocale : (state) => (locale) => {
		return state.currentQuestionType.translated_name[locale] ?? ""
	},

	tableSettings : state => {
		return state.tableSettings
	},
}