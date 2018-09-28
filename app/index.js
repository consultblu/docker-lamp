var inputData = {
  authorization: "AN:quickweightloss|WOsdjm_veYnn9wEdrmu1FGfohXH-5Uhx9limCufLy2SzcoBGPusvALGTebfMKZnNivnVk1aWr6cOIqZgRP-s3_I41HDwrQScbI4QtmxJ8OFIn_ZgP_E8Lqub4b9OWYnoPYFCnXKcWGxW_B0IYQyl27hOnhsvzd3stl89URhJtRAfL6QxowgNZoryVC8hhqyQ9xt3hbz6BYPZWHfDsh5Cr9wj0orcq9wG-oghuJdKA4d6MOBWPAUgsUlV_EzRgg9kCr0_5FgFdsGG0OCNFnxtpNbCEnKDlPoZSSg74w177HFfsLfxgtdeqUKijX6jH0XfAdzbkI1v3rNwQ5jztV41FgPNC19kbizImuYnAwjsYUaJ7gG8Jmct7FTiQ9kV7fQX56rcYQfARRCuBVPJYuAmuNvo-qlnh4FjGGhpIF_kQi-KT20lj4gOcMjLEmBnWoqH7Ru1xRyFVnlIamXFne3gpVjnFM3c68Gfd426KGpe5o9L7ykaPSBAPMcDmuYyETmXPT7KGTNmQQekJL6Ql0jaW6nUMtXRl5hfV19Y4j0lxScBNKrLebW1wnKPh7J7VCe28iDL9LgkN98vYXjuamRt19LMNQ0fI9680J6KkKDyvQT6uBdH9KNLIQm3pMTvHiwy5hJz7N3Dl_83vmH3o2zfIkriLhgp_h89MJS2tpvo24ueBubmvieC34Ai4CwriL166ZpkK-cvQqhlTnhm3NjFNPJgQSVg_YCG3IHFxyx0HrM",
  leadName: "Carlo,Smith",
  followUpDate: "2018-09-06",
  guestSearch: "false",
  guestId: "false",
  FirstName: 'Carlo',
  LastName: 'Smith',
  Email: 'carlo.smith@qwlc.net',
  Gender: '-1',
  MobileNumber: '9545796111',
  CenterId: '688a7791-c942-4e19-b166-5c52bf9d2e44'
};
//var http = require("fetch");
//var output = [];

var test = {
    "Guests": [
        {
            "Id": "e1ae5010-fc73-43e9-b598-a062903f590d",
            "Code": "SMG150186",
            "FirstName": "Evelyn",
            "MiddleName": null,
            "LastName": "Valdiviez",
            "Email": "evelynbvaldiviez@gmail.com",
            "MobileNumber": "5129474167",
            "MobilePhoneModel": {
                "CountryId": -1,
                "Number": "5129474167",
                "DisplayNumber": "5129474167"
            },
            "HomePhone": null,
            "HomePhoneModel": null,
            "WorkPhone": null,
            "WorkPhoneModel": null,
            "Gender": -1,
            "DateOfBirth": null,
            "AnniversaryDate": null,
            "Address1": null,
            "Address2": null,
            "City": null,
            "PostalCode": null,
            "State": null,
            "Country": null,
            "Nationality": null,
            "ReferralSource": null,
            "ReferredGuestId": null,
            "ReceiveTransactionalSms": false,
            "ReceiveMarketingEmail": null,
            "ReceiveTransactionalEmail": null,
            "ReceiveMarketingSms": false,
            "CreationDate": null,
            "LastUpdated": null,
            "MergeIntoCode": null,
            "MergeIntoGuestId": null,
            "CenterId": "688a7791-c942-4e19-b166-5c52bf9d2e44",
            "CenterCode": null,
            "CenterName": "",
            "GuestIndicator": null,
            "IsMember": 0,
            "DOB_IncompleteYear": null,
            "Password": null,
            "UserName": null,
            "FacebookUserId": null,
            "OldPassword": null,
            "OptInForLoyaltyProgram": null,
            "FormDetails": null,
            "IsMinors": false,
            "LpTierInfo": null
        }
    ],
    "Error": null,
    "Total": 1
}

console.log(test.Guests[0].Id);
