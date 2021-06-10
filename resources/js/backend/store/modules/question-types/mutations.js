export default{
	setQuestionTypeList ( state, response ) {
		state.questionTypeList = response.data
	},

	setCurrentQuestionType ( state, response ) {
		state.currentQuestionType = response
	},
	tableSettings ( state, response ) {
		state.tableSettings = response
	}
}