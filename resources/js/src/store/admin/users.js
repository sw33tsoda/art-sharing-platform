const { createSlice } = require("@reduxjs/toolkit");

const admin_usersSlice = createSlice({
    name: 'admin_users',
    initialState: {
        list: [],
        listRefreshTimes:0,
    },
    reducers: {
        setList: (state,action) => {
            state.list = action.payload;
        },
        userListRefresh: (state,action) => {
            state.listRefreshTimes += 1;
        }
    }
});

const { reducer:admin_usersReducer, actions } = admin_usersSlice;
export const { setList,userListRefresh } = actions;
export default admin_usersReducer;
